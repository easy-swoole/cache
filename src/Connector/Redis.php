<?php

/*
 * +-------------------------------------
 * | easySwoole framework unit
 * +-------------------------------------
 * | WebSite: https://www.easyswoole.com
 * +-------------------------------------
 * | Welcome Join QQGroup 633921431
 * +-------------------------------------
 */

namespace easySwoole\Cache\Connector;

use DateInterval;
use easySwoole\Cache\Exception\CacheException;

/**
 * Class Redis.
 * @author : evalor <master@evalor.cn>
 */
class Redis extends AbstractCache
{
    /* @var \Redis Instance */
    protected static $instance;

    protected $options = [
        'host'       => '127.0.0.1',  // Redis服务器
        'port'       => 6379,         // Redis端口
        'password'   => '',           // Redis密码
        'select'     => 0,            // Redis库序号
        'timeout'    => 0,            // 连接超时
        'expire'     => 0,            // 默认缓存超时
        'persistent' => false,        // 是否使用长连接
        'prefix'     => 'cache:',     // 缓存前缀
    ];

    /**
     * Redis constructor.
     * @param  array          $options
     * @throws CacheException
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            throw new CacheException('Cache Connector: not support redis');
        }

        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * Connection to redis service.
     * @author : evalor <master@evalor.cn>
     */
    protected function connect()
    {
        if (!is_object(self::$instance)) {
            $func = $this->options['persistent'] ? 'pconnect' : 'connect';

            self::$instance = new \Redis();
            self::$instance->$func($this->options['host'], $this->options['port'], $this->options['timeout']);

            if ('' != $this->options['password']) {
                self::$instance->auth($this->options['password']);
            }
            if (0 != $this->options['select']) {
                self::$instance->select($this->options['select']);
            }
        }
    }

    /**
     * Increment the value of the storage.
     * @param string   $name The name of the item in store.
     * @param int|null $step The value to increment, must be an integer.
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function inc($name, $step = 1)
    {
        return self::$instance->incrBy($this->getCacheKey($name), $step);
    }

    /**
     * Decrement the value of the storage.
     * @param string   $name The name of the item in store.
     * @param int|null $step The value to decrement, must be an integer.
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function dec($name, $step = 1)
    {
        return self::$instance->decrBy($this->getCacheKey($name), $step);
    }

    /**
     * Fetches a value from the cache and delete it.
     * @param  string $name    The name of the item in store.
     * @param  mixed  $default Default value to return if the key does not exist.
     * @return mixed
     * @author : evalor <master@evalor.cn>
     */
    public function pull($name, $default = null)
    {
        $result = $this->get($name, false);
        if ($result) {
            $this->delete($name);

            return $result;
        } else {
            return $default;
        }
    }

    /**
     * If the name does not exist, insert value.
     * @param  string                $name  The name of the item to store.
     * @param  mixed                 $value The value of the item to store, must be serializable.
     * @param  null|int|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    public function remember($name, $value, $ttl = null)
    {
        if (!$this->has($name)) {
            return $this->set($name, $value, $ttl);
        }

        return $this->get($name);
    }

    /**
     * Fetches a value from the cache.
     * @param string $name    The name of the item in store.
     * @param mixed  $default Default value to return if the key does not exist.
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $this->connect();
        $value = self::$instance->get($this->getCacheKey($name));
        if (is_null($value) || false === $value) {
            return $default;
        }

        return $this->unPackData($value);
    }

    /**
     * Persists data in the cache, uniquely referenced by a name with an optional expiration TTL time.
     * @param string                $name  The name of the item to store.
     * @param mixed                 $value The value of the item to store, must be serializable.
     * @param null|int|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function set($name, $value, $ttl = null)
    {
        $this->connect();
        if (is_null($ttl)) {
            $ttl = $this->options['expire'];
        }

        $key   = $this->getCacheKey($name);
        $ttl   = $this->getExpireTime($ttl);
        $value = $this->packData($value);

        if ($ttl) {
            $result = self::$instance->setex($key, $ttl, $value);
        } else {
            $result = self::$instance->set($key, $value);
        }

        return $result;
    }

    /**
     * Delete an item from the cache by its unique key.
     * @param string $name The name of the item in store.
     * @author : evalor <master@evalor.cn>
     * @return bool True on success and false on failure.
     */
    public function delete($name)
    {
        return self::$instance->delete($this->getCacheKey($name));
    }

    /**
     * Determines whether an item is present in the cache.
     * @param string $name The name of the item in store.
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function has($name)
    {
        return self::$instance->exists($this->getCacheKey($name));
    }

    /**
     * Wipes clean the entire cache's keys.
     * @author : evalor <master@evalor.cn>
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        $keys = self::$instance->keys($this->options['prefix'].'*');
        if ($keys) {
            self::$instance->del(...$keys);
        }

        return true;
    }

    /**
     * Get the cache driver instance.
     * @author : evalor <master@evalor.cn>
     * @return mixed the Driver instance
     */
    public function driver()
    {
        $this->connect();

        return self::$instance;
    }
}
