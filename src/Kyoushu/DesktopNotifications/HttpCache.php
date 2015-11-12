<?php

namespace Kyoushu\DesktopNotifications;

class HttpCache extends Cache
{

    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11';

    public function __construct($name)
    {
        parent::__construct(sprintf('http/%s', $name));
    }

    /**
     * @param string $url
     * @param int $ttl
     * @return string
     */
    public function get($url, $ttl = 0)
    {
        return parent::get($url, $ttl, function() use ($url, $ttl){

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $ttl);
            curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $html = curl_exec($ch);
            curl_close($ch);

            return $html;

        });
    }

}