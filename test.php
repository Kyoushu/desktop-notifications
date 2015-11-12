<?php

namespace Kyoushu\DesktopNotifications;

require_once(__DIR__ . '/vendor/autoload.php');

Notification::create('Test Message')
    ->setExpireTime(15000)
    ->send()
;