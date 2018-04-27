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

/**
 * Cache Connector
 * Class AbstractCache.
 * @author : evalor <master@evalor.cn>
 */
abstract class AbstractCache
{
    protected $options;
    protected $expire;

    /**
     * Increment the value of the storage.
     * @param string   $name The name of the item in store.
     * @param int|null $step The value to increment, must be an integer.
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) {
            $value  = $this->get($name) + $step;
            $expire = $this->expire;
        } else {
            $value  = $step;
            $expire = 0;
        }

        return $this->set($name, $value, $expire) ? $value : false;
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
        if ($this->has($name)) {
            $value  = $this->get($name) - $step;
            $expire = $this->expire;
        } else {
            $value  = -$step;
            $expire = 0;
        }

        return $this->set($name, $value, $expire) ? $value : false;
    }

    /**
     * Fetches a value from the cache and delete it.
     * @param  string $name    The name of the item in store.
     * @param  mixed  $default Default value to return if the key does not exist.
     * @return mixed
     * @author : evalor <master@evalor.cn>
     */
    abstract public function pull($name, $default = null);

    /**
     * If the name does not exist, insert value.
     * @param  string                $name  The name of the item to store.
     * @param  mixed                 $value The value of the item to store, must be serializable.
     * @param  null|int|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    abstract public function remember($name, $value, $ttl = null);

    /**
     * Fetches a value from the cache.
     * @param string $name    The name of the item in store.
     * @param mixed  $default Default value to return if the key does not exist.
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    abstract public function get($name, $default = null);

    /**
     * Persists data in the cache, uniquely referenced by a name with an optional expiration TTL time.
     * @param string                $name  The name of the item to store.
     * @param mixed                 $value The value of the item to store, must be serializable.
     * @param null|int|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    abstract public function set($name, $value, $ttl = null);

    /**
     * Delete an item from the cache by its unique key.
     * @param string $name The name of the item in store.
     * @author : evalor <master@evalor.cn>
     * @return bool True on success and false on failure.
     */
    abstract public function delete($name);

    /**
     * Determines whether an item is present in the cache.
     * @param string $name The name of the item in store.
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    abstract public function has($name);

    /**
     * Wipes clean the entire cache's keys.
     * @author : evalor <master@evalor.cn>
     * @return bool True on success and false on failure.
     */
    abstract public function clear();

    /**
     * Get the cache driver instance.
     * @author : evalor <master@evalor.cn>
     * @return mixed the Driver instance
     */
    abstract public function driver();

    /**
     * Turn the DateTime type into an integer type timestamp.
     * @param \DateTime|int|string $expire
     * @author : evalor <master@evalor.cn>
     * @return int
     */
    protected function getExpireTime($expire)
    {
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }

        return (int) $expire;
    }

    /**
     * Serialize the cache data.
     * @param $data
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    protected function packData($data)
    {
        return serialize($data);
    }

    /**
     * UnSerialize the cache data.
     * @param $data
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    protected function unPackData($data)
    {
        return unserialize($data);
    }

    /**
     * build cache key.
     * @param $name
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['prefix'].$name;
    }
}
