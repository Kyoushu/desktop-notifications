<?php

namespace Kyoushu\DesktopNotifications;

use Symfony\Component\Process\Process;

class Notification
{

    const ICON_ALERT = 'hook-notifier';
    const ICON_ERROR = 'error';

    const URGENCY_LOW = 'low';
    const URGENCY_NORMAL = 'normal';
    const URGENCY_CRITICAL = 'critical';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $icon = self::ICON_ALERT;

    /**
     * @var string
     */
    protected $urgency = self::URGENCY_NORMAL;

    /**
     * @var int milliseconds
     */
    protected $expireTime = 5000;

    /**
     * @param string $message
     * @param string $icon
     * @return Notification
     */
    public static function create($message, $icon = null)
    {
        return new Notification($message, $icon);
    }

    /**
     * @param string $message
     * @param string $icon
     */
    public function __construct($message, $icon = null)
    {
        $this->message = $message;
        if($icon !== null) $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * @param string $urgency
     * @return $this
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * @param int $expireTime milliseconds
     * @return $this
     */
    public function setExpireTime($expireTime)
    {
        $this->expireTime = (int)$expireTime;
        return $this;
    }

    public function send()
    {
        $cmd = sprintf(
            'notify-send --icon=%s --urgency=%s --expire-time=%s %s',
            escapeshellarg($this->icon),
            escapeshellarg($this->urgency),
            $this->expireTime,
            escapeshellarg($this->message)
        );

        echo $cmd;

        $process = new Process($cmd);
        $process->run();
    }

}