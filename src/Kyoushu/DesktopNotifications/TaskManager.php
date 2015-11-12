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
        $tasks =  array(
            new PackagistTask('accord/mandrill-swiftmailer-bundle', 3600),
            new PackagistTask('accord/mandrill-swiftmailer', 3600)
        );

        $tasks = array_merge($tasks, self::getCustomTasks());

        return $tasks;
    }

    /**
     * @return array
     */
    protected static function getCustomTasks()
    {
        $path = sprintf('%s/../../../custom_tasks.php', __DIR__);
        if(!file_exists($path)) return array();
        return include($path);
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