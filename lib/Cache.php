<?php

namespace lib;

use Psr\SimpleCache\CacheInterface as Psr16CacheInterface;

/**
 * 缓存类
 * Class Cache
 * @package lib
 */
class Cache implements Psr16CacheInterface
{

    public function __construct( ){

    }



    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {

        $value = Redis::get(unserialize($key));
        if ($value)
            return $value;
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {

        return $this->setMultiple([$key => $value], $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        if (!\is_array($keys)) {
            throw new InvalidArgumentException(sprintf('Cache keys must be array or Traversable, "%s" given', \is_object($keys) ? \get_class($keys) : \gettype($keys)));
        }
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;

    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!\is_array($values)) {
            throw new \Exception(sprintf('Cache values must be array or Traversable, "%s" given', \is_object($values) ? \get_class($values) : \gettype($values)));
        }

        try {
            foreach ($values as $key => $value) {
                if (\is_int($key)) {
                    $key = (string) $key;
                }
                Redis::set($key,serialize($value),$ttl);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
       if (!\is_array($keys)) {
            throw new Exception(sprintf('Cache keys must be array or Traversable, "%s" given', \is_object($keys) ? \get_class($keys) : \gettype($keys)));
        }
        try {
            Redis::delete($keys);
            return true;
        } catch (\Exception $e) {
            return false;
        }



    }

    public function clear()
    {

    }

    public function delete($key)
    {
        try {
            Redis::delete($key);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function has($key)
    {
        $value = Redis::get($key);
        if ($value)
            return true;
        return false;
    }
}
