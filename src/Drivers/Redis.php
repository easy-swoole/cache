<?php

namespace SwooleKit\Cache\Drivers;

use const false;
use SwooleKit\Cache\Config\RedisConfig;
use SwooleKit\Cache\Pools\RedisPool;
use Exception;
use Swoole\Coroutine;
/**
 * Redis缓存
 * Class Redis
 * @package Drivers
 */
class Redis extends AbstractDriver
{
    protected $config;
    protected $redis;
    protected $context = array();

    /**
     * Redis constructor.
     * @param null $config
     */
    public function __construct(RedisConfig $config = null)
    {
        if (is_null($config)) {
            $config = new RedisConfig;
        }
        $this->config = $config;
    }

    /**
     * 获取连接
     * @return RedisPool
     * @throws Exception
     */
    private function getClient()
    {
        if (!$this->redis) {
            $this->redis = new RedisPool($this->config);
        }
        if(!$this->redis){
            throw new Exception('Redis Pool: empty!');
        }
        // 协程结束自动回收链接
        $coroutineId = Coroutine::getCid();
        if (!isset($this->context[$coroutineId])) {
            $this->context[$coroutineId] = $this->redis->getObj();;
            Coroutine::defer(function () use ($coroutineId) {
                $this->redis->recycleObj($this->context[$coroutineId]);
            });
        }
        return $this->redis;
    }

    /**
     * 写入缓存
     * @param string $key
     * @param mixed $value
     * @param null $expire
     * @return mixed
     */
    public function set($key, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = 0;
        }
        $value =  $this->serialize($value);
        $client = $this->getClient();
        if ($expire) {
            $result = $client->setEx($key, $expire, $value);
        } else {
            $result = $client->set($key, $value);
        }
        return $result;
    }

    /**
     * 读取缓存
     * @param string $key
     * @param null $default
     * @return bool|mixed
     */
    public function get($key, $default = null)
    {
        $client = $this->getClient();
        $value=$client->get($key);
        if (is_null($value) || false === $value) {
            return false;
        }
        $content = $this->unserialize($value);
        return $content;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @param $key
     * @param int $step
     * @return bool|int|mixed|string
     */
    public function inc($key, $step = 1)
    {
        $client = $this->getClient();
        return $client->incrby($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @param $key
     * @param int $step
     * @return bool|int|mixed|string
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
     */
    public function delete($key)
    {
        $client = $this->getClient();
        return $client->del($key);
    }

    /**
     * 清空缓存
     * @return mixed
     */
    public function clear()
    {
        $client = $this->getClient();
        return $client->flushDB();
    }
}