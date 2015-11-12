<?php

namespace Kyoushu\DesktopNotifications;

class Cache {

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     * @return Cache
     */
    public static function create($name)
    {
        return new self($name);
    }

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $dir = $this->getDir();
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
    }

    public function getDir()
    {
        return sprintf('%s/../../../cache/%s', __DIR__, $this->name);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function createKeyHash($key)
    {
        return sha1(sprintf(
            '%s-%s',
            $this->name,
            $key
        ));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function createKeyPath($key)
    {
        $keyHash = $this->createKeyHash($key);
        return sprintf('%s/%s/%s/%s.ser', $this->getDir(), substr($keyHash, 0, 4), substr($keyHash, 4, 4), $keyHash);
    }

    /**
     * @param string $key
     */
    public function invalidate($key)
    {
        $path = $this->createKeyPath($key);
        if(!file_exists($path)) return;
        unlink($path);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $serialized = serialize($value);
        $path = $this->createKeyPath($key);
        $dir = dirname($path);
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        $handle = fopen($path, 'w');
        fwrite($handle, $serialized);
        fclose($handle);
        chmod($path, 0666);
        return $this;
    }

    /**
     * @param string $key
     * @param \Closure|null $callback
     * @return null|mixed
     */
    protected function defaultValue($key, \Closure $callback = null)
    {
        if($callback === null) return null;
        $value = $callback();
        $this->set($key, $value);
        return $value;
    }

    /**
     * @param string $path
     * @return int|null
     */
    protected function getPathAge($path)
    {
        if(!file_exists($path)) return null;
        $modified = filemtime($path);
        $now = time();
        return $now - $modified;
    }

    /**
     * @param string $key
     * @param int $ttl
     * @param \Closure|null $defaultValueCallback
     * @return null|mixed
     */
    public function get($key, $ttl = 0, \Closure $defaultValueCallback = null)
    {
        $ttl = (int)$ttl;
        if($ttl === 0) return $this->defaultValue($key, $defaultValueCallback);
        $path = $this->createKeyPath($key);
        if(!file_exists($path)) return $this->defaultValue($key, $defaultValueCallback);
        if($this->getPathAge($path) > $ttl && $ttl !== -1) return $this->defaultValue($key, $defaultValueCallback);
        return unserialize(file_get_contents($path));
    }

}