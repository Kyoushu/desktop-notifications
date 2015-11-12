<?php

namespace Kyoushu\DesktopNotifications;

use Kyoushu\DesktopNotifications\Task\PackagistTask;
use Kyoushu\DesktopNotifications\Task\TaskInterface;
use Psr\Log\LoggerInterface;

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

    /**
     * @param LoggerInterface $logger
     */
    public static function processTasks(LoggerInterface $logger)
    {
        foreach(self::getTasks() as $task){
            foreach($task->getNotifications($logger) as $notification){
                $notification->send();
            }
        }
    }

}