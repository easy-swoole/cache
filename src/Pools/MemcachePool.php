<?php

namespace SwooleKit\Cache\Pools;

use EasySwoole\Component\Pool\AbstractPool;
use SwooleKit\Cache\Config\MemcachedConfig;
use SwooleKit\Cache\Memcache\MemcacheClient;

/**
 * Memcache Pools
 * Class MemcachePool
 * @package Pools
 */
class MemcachePool extends AbstractPool
{
    /**
     * 创建客户端对象
     * @return MemcacheClient
     */
    protected function createObject()
    {
        /** @var MemcachedConfig $memcacheConfig */
        $memcacheConfig = $this->getConfig();
        $memcacheClient = new MemcacheClient($memcacheConfig->getHost(), $memcacheConfig->getPort());
        return $memcacheClient;
    }
}