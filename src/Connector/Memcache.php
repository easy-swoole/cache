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
 * Class Memcache.
 *
 * @author : evalor <master@evalor.cn>
 */
class Memcache extends AbstractCache
{
    /* @var \Memcache Instance */
    protected static $instance;

    protected $options = [
        'host'       => '127.0.0.1',  // Memcache服务器
        'port'       => 11211,        // Memcache端口
        'expire'     => 0,            // 默认缓存过期时间
        'timeout'    => 0,            // 连接超时
        'persistent' => true,         // 是否启用长连接
        'prefix'     => '',           // 缓存后缀
    ];

    /**
     * Memcache constructor.
     *
     * @param array $options
     *
     * @throws CacheException
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('memcache')) {
            throw new CacheException('Cache Connector: not support memcache');
        }

        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * Connection to memcache service.
     *
     * @author : evalor <master@evalor.cn>
     */
    protected function connect()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new \Memcache();

            // Cluster connections
            $hosts = explode(',', $this->options['host']);
            $ports = explode(',', $this->options['port']);
            if (empty($ports[0])) {
                $ports[0] = 11211;
            }

            // connection establishment
            foreach ((array) $hosts as $i => $host) {
                $port = isset($ports[$i]) ? $ports[$i] : $ports[0];
                $this->options['timeout'] > 0 ?
                    self::$instance->addServer($host, $port, $this->options['persistent'], 1, $this->options['timeout']) :
                    self::$instance->addServer($host, $port, $this->options['persistent'], 1);
            }
        }
    }

    /**
     * Increment the value of the storage.
     *
     * @param string   $name The name of the item in store.
     * @param int|null $step The value to increment, must be an integer.
     *
     * @author : evalor <master@evalor.cn>
     *
     * @return bool
     */
    public function inc($name, $step = 1)
    {
        $key = $this->getCacheKey($name);
        if (self::$instance->get($key)) {
            return self::$instance->increment($key, $step);
        }

        return self::$instance->set($key, $step);
    }

    /**
     * Decrement the value of the storage.
     *
     * @param string   $name The name of the item in store.
     * @param int|null $step The value to decrement, must be an integer.
     *
     * @author : evalor <master@evalor.cn>
     *
     * @return bool|int
     */
    public function dec($name, $step = 1)
    {
        $key   = $this->getCacheKey($name);
        $value = self::$instance->get($key) - $step;

        return self::$instance->set($key, $value);
    }

    /**
     * Fetches a value from the cache and delete it.
     *
     * @param string $name    The name of the item in store.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed
     *
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
     *
     * @param string                $name  The name of the item to store.
     * @param mixed                 $value The value of the item to store, must be serializable.
     * @param null|int|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *
     * @return bool
     *
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
     *
     * @param string $name    The name of the item in store.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @author : evalor <master@evalor.cn>
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $this->connect();
        $result = self::$instance->get($this->getCacheKey($name));

        return false !== $result ? $this->unPackData($result) : $default;
    }

    /**
     * Persists data in the cache, uniquely referenced by a name with an optional expiration TTL time.
     *
     * @param string                $name  The name of the item to store.
     * @param mixed                 $value The value of the item to store, must be serializable.
     * @param null|int|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *
     * @author : evalor <master@evalor.cn>
     *
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

        return self::$instance->set($key, $value, 0, $ttl);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $name The name of the item in store.
     *
     * @author : evalor <master@evalor.cn>
     *
     * @return bool True on success and false on failure.
     */
    public function delete($name)
    {
        $this->connect();
        $key = $this->getCacheKey($name);

        return self::$instance->delete($key);
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $name The name of the item in store.
     *
     * @author : evalor <master@evalor.cn>
     *
     * @return bool
     */
    public function has($name)
    {
        $this->connect();
        $key = $this->getCacheKey($name);

        return self::$instance->get($key) ? true : false;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @author : evalor <master@evalor.cn>
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        $this->connect();

        return self::$instance->flush();
    }

    public function driver()
    {
        $this->connect();

        return self::$instance;
    }
}
