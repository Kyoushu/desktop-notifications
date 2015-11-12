<?php

namespace Kyoushu\DesktopNotifications;

require_once(__DIR__ . '/vendor/autoload.php');

try{
    TaskManager::processTasks();
}
catch(\Exception $e){
    Notification::create($e->getMessage(), Notification::ICON_ERROR)->send();
}