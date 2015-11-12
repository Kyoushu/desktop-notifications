<?php

namespace Kyoushu\DesktopNotifications\Task;

use Kyoushu\DesktopNotifications\Cache;
use Kyoushu\DesktopNotifications\Exception\DesktopNotificationsException;
use Kyoushu\DesktopNotifications\Notification;
use Psr\Log\LoggerInterface;

class NonChanTask
{

    protected $username;

    protected $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->cache = new Cache(sprintf('nonchan/%s', $this->username));
    }

    /**
     * @var Cache
     */
    protected $cache;

    protected function call($endpointUrl, array $data, $token = null)
    {
        $url = sprintf(
            'https://nonchan.co.uk%s%s',
            $endpointUrl,
            ($token ? '?token=' . urlencode($token) : '')
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $responseJson = curl_exec($ch);

        $error = curl_error($ch);
        if($error){
            throw new DesktopNotificationsException('NonChan: ' . $error);
        }

        if(!$responseJson){
            throw new DesktopNotificationsException('NonChan: API response empty');
        }

        curl_close($ch);

        $response = json_decode($responseJson, true);
        if($response['status'] !== 'ok'){
            throw new DesktopNotificationsException('NonChan: ' . $response['reason']);
        }

        return $response;
    }

    protected function getToken()
    {
        return $this->cache->get('token', 3600, function(){
            $response = $this->call('/api/Users/login', array($this->username, $this->password));
            return $response['data'][1];
        });
    }

    /**
     * @return \DateTime
     */
    protected function getLastChecked()
    {
        return $this->cache->get('last_checked', -1, function(){
            return new \DateTime('now');
        });
    }

    protected function updateLastChecked(\DateTime $datetime)
    {
        $this->cache->set('last_checked', $datetime);
    }

    protected function getUpdates()
    {
        $token = $this->getToken();
        return $this->call('/api/Notifications/getUpdates', array(
            $this->getLastChecked()->format('U')
        ), $token);
    }

    public function getNotifications(LoggerInterface $logger)
    {

        $updates = $this->getUpdates();

        $notifications = array();

        if($updates['data'][1]){
            foreach($updates['data'][1] as $message){
                $message = strip_tags($message);
                $notifications[] = Notification::create($message, 'notification-message-im')->setUrgency(Notification::URGENCY_CRITICAL);
            }
        }

        $lastChecked = \DateTime::createFromFormat('U', $updates['data'][0]);

        $this->updateLastChecked($lastChecked);

        return $notifications;

    }

}