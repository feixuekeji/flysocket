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
    /**
     * 驱动句柄
     * @var object
     */
    protected $handler = null;

    /**
     * 缓存参数
     * @var array
     */
    protected $options = [
        'expire'     => 0,
        'prefix'     => '',
        'serialize'  => true,
    ];
    public function __construct($options = []){
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = Redis::getInstance();
    }



    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        $key    = $this->getCacheKey($name);
        $value = $this->handler->get($key);
        if (is_null($value) || false === $value) {
            return $default;
        }
        return $this->unserialize($value);
    }

    /**
     * 写入缓存
     * @access public
     * @param  string            $name 缓存变量名
     * @param  mixed             $value  存储数据
     * @param  integer|\DateTime $expire  有效时间（秒）
     * @return boolean
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $value = $this->serialize($value);
        $key    = $this->getCacheKey($name);
        if ($expire) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }
        return $result;

    }

    /**
     * 批量获取
     * @param iterable $keys
     * @param null $default
     * @return array|iterable
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     *批量设置
     * @param iterable $values
     * @param null $expire
     * @return bool
     * @throws \Exception
     * @author xxf
     * @date 2020-08-26 15:37
     */
    public function setMultiple($values, $expire = null)
    {
        if (!\is_array($values)) {
            throw new \Exception(sprintf('Cache values must be array or Traversable, "%s" given', \is_object($values) ? \get_class($values) : \gettype($values)));
        }
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        try {
            foreach ($values as $key => $value) {
                if (\is_int($key)) {
                    $key = (string) $key;
                }
                $this->set($key,$value,$expire);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function delete($name)
    {
        $key = $this->getCacheKey($name);
        try {
            $this->handler->del($key);
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
       foreach ($keys as &$item){
           $item = $this->getCacheKey($item);
       }

        try {
            $this->handler->del($keys);
            return true;
        } catch (\Exception $e) {
            return false;
        }



    }

    /**
     * 清除缓存
     * @access public
     * @param  string $tag 标签名
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flushDB();
    }




    /**
     * 判断缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        $key = $this->getCacheKey($name);
        return $this->handler->exists($key);
    }


    /**
     * 序列化数据
     * @access protected
     * @param  mixed $data
     * @return string
     */
    protected function serialize($data)
    {
        if (is_scalar($data) || !$this->options['serialize']) {
            return $data;
        }
        $data = 'serialize:'.serialize($data);
        return $data;
    }

    /**
     * 反序列化数据
     * @access protected
     * @param  string $data
     * @return mixed
     */
    protected function unserialize($data)
    {
        if ($this->options['serialize'] && 0 === strpos($data, 'serialize:')) {
            return unserialize(substr($data, 10));
        } else {
            return $data;
        }
    }

    /**
     * 获取实际的缓存标识
     * @access protected
     * @param  string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['prefix'] . $name;
    }

}
