<?php

namespace Kyoushu\DesktopNotifications;

use Symfony\Component\Process\Process;

class Notification
{

    const ICON_ALERT = 'hook-notifier';
    const ICON_ERROR = 'error';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $icon;

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

    public function send()
    {
        $cmd = sprintf(
            'notify-send --icon=%s %s',
            escapeshellarg($this->icon),
            escapeshellarg($this->message)
        );

        echo $cmd;

        $process = new Process($cmd);
        $process->run();
    }

}