<?php

namespace Kyoushu\DesktopNotifications\Task;

use Kyoushu\DesktopNotifications\Notification;
use Psr\Log\LoggerInterface;

interface TaskInterface
{

    /**
     * @param LoggerInterface $logger
     * @return Notification[]
     */
    public function getNotifications(LoggerInterface $logger);

}