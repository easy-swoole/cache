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

use EasySwoole\Cache\Config\MemcacheConfig;
use EasySwoole\Cache\Exception\CacheException;
use EasySwoole\Cache\Memcache\MemcacheClient;
use EasySwoole\Cache\Memcache\Status;
use EasySwoole\Cache\Pools\MemcachePool;
use EasySwoole\Component\Pool\Exception\PoolObjectNumError;
use Swoole\Coroutine;
use Throwable;

/**
 * Memcache缓存
 * Class Memcached.
 */
class Memcache extends AbstractDriver
{
    /**
     * memcacheConfig.
     * @var MemcacheConfig
     */
    protected $memcacheConfig;

    /**
     * MemcacheClients.
     * @var MemcacheClient[]
     */
    protected $memcacheContext = [];

    /**
     * MemcachePool.
     * @var MemcachePool
     */
    protected $memcachePool;

    /**
     * execTimeout.
     * @var int
     */
    protected $execTimeout;

    /**
     * Memcached constructor.
     * @param MemcacheConfig $memcacheConfig
     * @param null           $execTimeout
     */
    public function __construct(MemcacheConfig $memcacheConfig = null, $execTimeout = null)
    {
        if (is_null($memcacheConfig)) {
            $memcacheConfig = new MemcacheConfig();
        }
        $this->memcacheConfig = $memcacheConfig;
        $this->execTimeout    = $execTimeout;
    }

    /**
     * 获取协程客户端.
     * @throws PoolObjectNumError
     * @throws Throwable
     * @return MemcacheClient
     */
    private function getClient(): MemcacheClient
    {
        if (!$this->memcachePool) {
            $this->memcachePool = new MemcachePool($this->memcacheConfig);
        }

        // 协程结束自动回收链接
        $coroutineId = Coroutine::getuid();
        if (!isset($this->memcacheContext[$coroutineId])) {
            $this->memcacheContext[$coroutineId] = $this->memcachePool->getObj();
            defer(function () use ($coroutineId) {
                $this->memcachePool->recycleObj($this->memcacheContext[$coroutineId]);
            });
        }

        return $this->memcacheContext[$coroutineId];
    }

    /**
     * 获取缓存.
     * @param  string             $key
     * @param  null               $default
     * @throws PoolObjectNumError
     * @throws Throwable
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $client = $this->getClient();

        try {
            return $client->get($key, $this->execTimeout);
        } catch (CacheException $cacheException) {
            if ($cacheException->getCode() == Status::STAT_KEY_NOTFOUND) {
                return $default;
            }

            throw $cacheException;
        }
    }

    /**
     * 设置缓存.
     * @param  string             $key
     * @param  mixed              $value
     * @param  int|null           $ttl
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        $client = $this->getClient();

        return $client->set($key, $value, $ttl, $this->execTimeout);
    }

    /**
     * 删除缓存.
     * @param  string             $key
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     * @return bool
     */
    public function delete($key)
    {
        $client = $this->getClient();

        return $client->delete($key, $this->execTimeout);
    }

    /**
     * 清空缓存.
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     * @return bool
     */
    public function clear()
    {
        $client = $this->getClient();

        return $client->flush(null, $this->execTimeout);
    }

    /**
     * 缓存是否存在.
     * @param  string             $key
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     * @return bool
     */
    public function has($key)
    {
        $client = $this->getClient();

        try {
            $client->get($key, $this->execTimeout);

            return true;
        } catch (CacheException $cacheException) {
            if ($cacheException->getCode() == Status::STAT_KEY_NOTFOUND) {
                return false;
            }

            throw $cacheException;
        }
    }
}
