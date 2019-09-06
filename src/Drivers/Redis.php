<?php

namespace SwooleKit\Cache\Drivers;

use EasySwoole\Component\Pool\Exception\PoolObjectNumError;
use Swoole\Coroutine;
use SwooleKit\Cache\Config\RedisConfig;
use SwooleKit\Cache\Pools\RedisPool;
use SwooleKit\Cache\Pools\RedisPoolObject;
use Throwable;

/**
 * Redis缓存
 * Class Redis
 * @package Drivers
 */
class Redis extends AbstractDriver
{

    /**
     * redisConfig
     * @var RedisConfig
     */
    protected $redisConfig;

    /**
     * redisClients
     * @var RedisPoolObject[]
     */
    protected $redisContext = array();

    /**
     * redisPool
     * @var RedisPool
     */
    protected $redisPool;

    /**
     * Redis constructor.
     * @param RedisConfig|null $redisConfig
     */
    public function __construct(RedisConfig $redisConfig = null)
    {
        if (is_null($redisConfig)) {
            $redisConfig = new RedisConfig;
        }

        $this->redisConfig = $redisConfig;
    }

    /**
     * 获取连接
     * @return RedisPoolObject
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    private function getClient()
    {
        if (!$this->redisPool) {
            $this->redisPool = new RedisPool($this->redisConfig);
        }

        // 协程结束自动回收链接
        $coroutineId = Coroutine::getuid();
        if (!isset($this->redisContext[$coroutineId])) {
            $this->redisContext[$coroutineId] = $this->redisPool->getObj();;
            defer(function () use ($coroutineId) {
                $this->redisPool->recycleObj($this->redisContext[$coroutineId]);
            });
        }

        return $this->redisContext[$coroutineId];
    }

    /**
     * 写入缓存
     * 已开启内置序列化支持
     * @param string $key
     * @param mixed $value
     * @param null $expire
     * @return mixed
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function set($key, $value, $expire = null)
    {
        $client = $this->getClient();
        if ($expire) {
            return $client->setex($key, intval($expire), $value);
        } else {
            return $client->set($key, $value);
        }
    }

    /**
     * 读取缓存
     * @param string $key
     * @param null $default
     * @return bool|mixed
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function get($key, $default = null)
    {
        $client = $this->getClient();
        $value = $client->get($key);
        return $value === false ? $default : $value;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @param $key
     * @param int $step
     * @return bool|int|mixed|string
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function inc($key, $step = 1)
    {
        $client = $this->getClient();
        return $client->incrBy($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @param $key
     * @param int $step
     * @return bool|int|mixed|string
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function dec($key, $step = 1)
    {
        $client = $this->getClient();
        return $client->decrby($key, $step);
    }

    /**
     * 判断缓存是否存在
     * @param string $key
     * @return mixed
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function has($key)
    {
        $client = $this->getClient();
        return $client->exists($key);
    }

    /**
     * 删除缓存
     * @param string $key
     * @return mixed
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function delete($key)
    {
        $client = $this->getClient();
        return $client->del($key);
    }

    /**
     * 清空缓存
     * @return mixed
     * @throws PoolObjectNumError
     * @throws Throwable
     */
    public function clear()
    {
        $client = $this->getClient();
        return $client->flushDB();
    }
}