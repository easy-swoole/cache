<?php

namespace SwooleKit\Cache\Pools;

use EasySwoole\Component\Pool\AbstractPool;
use SwooleKit\Cache\Config\RedisConfig;

/**
 * Redis Pools
 * Class RedisPool
 * @package SwooleKit\Cache\Pools
 */
class RedisPool extends AbstractPool
{
    protected function createObject()
    {
        $config = new RedisConfig;
        $redis = new RedisPoolObject;
        $connected = $redis->connect($config->getHost(), $config->getPort());
        if ($connected) {
            if ($config->getAuth()) {
                $redis->auth($config->getAuth());
            }
            if ($config->getDb()) {
                $redis->select($config->getDb());
            }
            return $redis;
        }
        return null;
    }
}