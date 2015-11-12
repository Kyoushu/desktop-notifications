<?php

namespace Kyoushu\DesktopNotifications\Task;

use Kyoushu\DesktopNotifications\Notification;

interface TaskInterface
{

    /**
     * @return Notification[]
     */
    public function getNotifications();

}