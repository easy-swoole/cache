<?php

namespace SwooleKit\Cache\Drivers;

use EasySwoole\Component\Pool\Exception\PoolObjectNumError;
use Swoole\Coroutine;
use SwooleKit\Cache\Config\MemcacheConfig;
use SwooleKit\Cache\Exception\CacheException;
use SwooleKit\Cache\Memcache\MemcacheClient;
use SwooleKit\Cache\Memcache\Status;
use SwooleKit\Cache\Pools\MemcachePool;
use Throwable;

/**
 * Memcache缓存
 * Class Memcached
 * @package Drivers
 */
class Memcache extends AbstractDriver
{
    /**
     * memcacheConfig
     * @var MemcacheConfig
     */
    protected $memcacheConfig;

    /**
     * MemcacheClients
     * @var MemcacheClient[]
     */
    protected $memcacheContext = array();

    /**
     * MemcachePool
     * @var MemcachePool
     */
    protected $memcachePool;

    /**
     * execTimeout
     * @var integer
     */
    protected $execTimeout;

    /**
     * Memcached constructor.
     * @param MemcacheConfig $memcacheConfig
     * @param null $execTimeout
     */
    public function __construct(MemcacheConfig $memcacheConfig = null, $execTimeout = null)
    {
        if (is_null($memcacheConfig)) {
            $memcacheConfig = new MemcacheConfig;
        }
        $this->memcacheConfig = $memcacheConfig;
        $this->execTimeout = $execTimeout;
    }

    /**
     * 获取协程客户端
     * @return MemcacheClient
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    private function getClient(): MemcacheClient
    {
        if (!$this->memcachePool) {
            $this->memcachePool = new MemcachePool($this->memcacheConfig);
        }

        // 协程结束自动回收链接
        $coroutineId = Coroutine::getuid();
        if (!isset($this->memcacheContext[$coroutineId])) {
            $this->memcacheContext[$coroutineId] = $this->memcachePool->getObj();;
            defer(function () use ($coroutineId) {
                $this->memcachePool->recycleObj($this->memcacheContext[$coroutineId]);
            });
        }

        return $this->memcacheContext[$coroutineId];
    }


    /**
     * 获取缓存
     * @param string $key
     * @param null $default
     * @return mixed
     * @throws PoolObjectNumError
     * @throws Throwable
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
     * 设置缓存
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return bool
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function set($key, $value, $ttl = null)
    {
        $client = $this->getClient();
        return $client->set($key, $value, $ttl, $this->execTimeout);
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function delete($key)
    {
        $client = $this->getClient();
        return $client->delete($key, $this->execTimeout);
    }

    /**
     * 清空缓存
     * @return bool
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function clear()
    {
        $client = $this->getClient();
        return $client->flush(null, $this->execTimeout);
    }

    /**
     * 缓存是否存在
     * @param string $key
     * @return bool
     * @throws CacheException
     * @throws PoolObjectNumError
     * @throws Throwable
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