<?php

namespace Kyoushu\DesktopNotifications;

use Kyoushu\DesktopNotifications\Task\PackagistTask;
use Kyoushu\DesktopNotifications\Task\TaskInterface;

class TaskManager
{

    /**
     * @return TaskInterface[]
     */
    public static function getTasks()
    {
        return array(
            new PackagistTask('accord/mandrill-swiftmailer-bundle', 3600),
            new PackagistTask('accord/mandrill-swiftmailer', 3600)
        );
    }

    public static function processTasks()
    {
        foreach(self::getTasks() as $task){
            foreach($task->getNotifications() as $notification){
                $notification->send();
            }
        }
    }

}