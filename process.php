<?php

namespace Kyoushu\DesktopNotifications;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once(__DIR__ . '/vendor/autoload.php');

$logger = new Logger('log', array(
    new StreamHandler(__DIR__ . '/logs/notification.log'),
    new StreamHandler('php://output'),
));

try{
    TaskManager::processTasks($logger);
}
catch(\Exception $e){
    Notification::create($e->getMessage(), Notification::ICON_ERROR)->send();
}