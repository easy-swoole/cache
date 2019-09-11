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

namespace EasySwoole\Cache\Pools;

use EasySwoole\Cache\Config\RedisConfig;
use EasySwoole\Component\Pool\AbstractPool;

/**
 * Redis Pools
 * Class RedisPool.
 */
class RedisPool extends AbstractPool
{
    /**
     * 创建客户端对象
     * @return RedisPoolObject|null
     */
    protected function createObject()
    {
        /** @var RedisConfig $redisConfig */
        $redisConfig = $this->getConfig();
        $redisClient = new RedisPoolObject();
        $redisClient->connect($redisConfig->getHost(), $redisConfig->getPort());

        // 设置当前客户端的配置参数
        $redisClient->setOptions([
            'timeout'            => $redisConfig->getExecTimeout(),
            'serialize'          => $redisConfig->isSerialize(),
            'reconnect'          => $redisConfig->getReconnect(),
            'connect_timeout'    => $redisConfig->getConnectTimeout(),
            'compatibility_mode' => $redisConfig->isCompatibilityMode(),
        ]);

        !is_null($redisConfig->getDb()) && $redisClient->select($redisConfig->getDb());
        !is_null($redisConfig->getAuth()) && $redisClient->auth($redisConfig->getAuth());

        return $redisClient;
    }
}
