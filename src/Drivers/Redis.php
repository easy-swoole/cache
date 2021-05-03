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

namespace EasySwoole\Cache\Drivers;

use EasySwoole\RedisPool\Pool;
use Throwable;
use EasySwoole\Pool\Exception\PoolEmpty;

/**
 * Redis缓存
 * Class Redis.
 */
class Redis extends AbstractDriver
{
    /**
     * @var Pool
     */
    protected $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $expire
     * @return bool|mixed
     * @throws Throwable
     * @throws PoolEmpty
     */
    public function set($key, $value, $expire = null)
    {
        return $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) use ($key, $value, $expire) {
            if ($expire) {
                return $redis->setEx($key, intval($expire), $value);
            } else {
                return $redis->set($key, $value);
            }
        });

    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     * @throws Throwable
     * @throws PoolEmpty
     */
    public function get($key, $default = null)
    {
        $value = $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) use ($key) {
            return $redis->get($key);
        });

        return $value === false ? $default : $value;
    }

    /**
     * @param $key
     * @param int $step
     * @return false|mixed
     * @throws PoolEmpty
     * @throws Throwable
     */
    public function inc($key, $step = 1)
    {
        return $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) use ($key, $step) {
            return $redis->incrBy($key, $step);
        });
    }

    /**
     * @param $key
     * @param int $step
     * @return false|mixed
     * @throws PoolEmpty
     * @throws Throwable
     */
    public function dec($key, $step = 1)
    {
        return $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) use ($key, $step) {
            return $redis->decrBy($key, $step);
        });
    }

    /**
     * @param string $key
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws Throwable
     */
    public function has($key)
    {
        return $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) use ($key) {
            return $redis->exists($key);
        });
    }

    /**
     * @param string $key
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws Throwable
     */
    public function delete($key)
    {
        return $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) use ($key) {
            return $redis->del($key);
        });
    }

    /**
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws Throwable
     */
    public function clear()
    {
        return $this->pool->invoke(function (\EasySwoole\Redis\Redis $redis) {
            return $redis->flushDb();
        });
    }
}
