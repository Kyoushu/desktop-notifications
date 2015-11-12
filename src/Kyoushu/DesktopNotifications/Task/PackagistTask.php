<?php

namespace Kyoushu\DesktopNotifications\Task;

use Kyoushu\DesktopNotifications\Cache;
use Kyoushu\DesktopNotifications\Exception\DesktopNotificationsException;
use Kyoushu\DesktopNotifications\Notification;
use Psr\Log\LoggerInterface;

class PackagistTask implements  TaskInterface
{

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var int
     */
    protected $checkInterval;

    /**
     * @var int
     */
    protected $downloadInterval;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param string $packageName e.g. accord/mandrill-swiftmailer-bundle
     * @param int $checkInterval
     * @param int $downloadInterval
     */
    public function __construct($packageName, $checkInterval, $downloadInterval = 100)
    {
        $this->packageName = $packageName;
        $this->cache = new Cache(sprintf('packagist/%s', $this->packageName));

        $this->checkInterval = (int)$checkInterval;
        $this->downloadInterval = (int)$downloadInterval;
    }

    /**
     * @return \DateTime|null
     */
    protected function getLastChecked()
    {
        return $this->cache->get('last_checked', -1, function(){
            return null;
        });
    }

    /**
     * @param \DateTime $lastChecked
     * @return $this
     */
    protected function setLastChecked(\DateTime $lastChecked)
    {
        $this->cache->set('last_checked', $lastChecked);
        return $this;
    }

    /**
     * @return array|null
     */
    protected function getLastStats()
    {
        return $this->cache->get('last_stats', -1, function(){
            return null;
        });
    }

    /**
     * @param array $lastStats
     * @return $this
     */
    protected function setLastStats(array $lastStats)
    {
        $this->cache->set('last_stats', $lastStats);
        return $this;
    }

    /**
     * @return string
     */
    protected function getStatsUrl()
    {
        return sprintf('https://packagist.org/packages/%s/stats.json', $this->packageName);
    }

    /**
     * @return array
     * @throws DesktopNotificationsException
     */
    protected function getStats()
    {
        $url = $this->getStatsUrl();
        $json = file_get_contents($url);
        if(!$json){
            throw new DesktopNotificationsException(sprintf('Error fetching %s', $url));
        }
        $stats = json_decode($json, true);
        return $stats;
    }

    /**
     * @return bool
     */
    protected function isCheckPending()
    {
        $lastChecked = $this->getLastChecked();
        if($lastChecked === null) return true;

        $now = new \DateTime('now');

        $diff = $now->format('U') - $lastChecked->format('U');
        return $diff > $this->checkInterval;
    }

    public function getNotifications(LoggerInterface $logger)
    {
        if(!$this->isCheckPending()){
            $logger->info(
                'no check pending',
                array(
                    'package_name' => $this->packageName
                )
            );
            return array();
        }

        $stats = $this->getStats();
        $lastStats = $this->getLastStats();

        if($lastStats === null){
            $this->setLastStats($stats);
        }

        $this->setLastChecked(new \DateTime('now'));

        if($lastStats !== null){
            $downloadDiff = $stats['downloads']['total'] - $lastStats['downloads']['total'];
            if($downloadDiff < $this->downloadInterval){

                $logger->info(
                    'download total diff has not passed interval',
                    array(
                        'package_name' => $this->packageName,
                        'diff' => $downloadDiff,
                        'interval' => $this->downloadInterval
                    )
                );

                return array();
            }
        }

        $this->setLastStats($stats);

        return array(
            Notification::create(sprintf(
                '%s just passed %s downloads!',
                $this->packageName,
                $stats['downloads']['total']
            ), 'face-smile-big-symbolic')->setUrgency(Notification::URGENCY_CRITICAL)
        );
    }


}