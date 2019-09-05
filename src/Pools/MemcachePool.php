<?php

namespace SwooleKit\Cache\Pools;

use EasySwoole\Component\Pool\AbstractPool;
use SwooleKit\Cache\Config\MemcacheConfig;
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
        /** @var MemcacheConfig $memcacheConfig */
        $memcacheConfig = $this->getConfig();
        $memcacheClient = new MemcacheClient($memcacheConfig->getHost(), $memcacheConfig->getPort());
        return $memcacheClient;
    }
}