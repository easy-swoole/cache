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

use EasySwoole\Cache\Config\MemcacheConfig;
use EasySwoole\Component\Pool\AbstractPool;

/**
 * Memcache Pools
 * Class MemcachePool.
 */
class MemcachePool extends AbstractPool
{
    /**
     * 创建客户端对象
     * @return MemcachePoolObject
     */
    protected function createObject()
    {
        /** @var MemcacheConfig $memcacheConfig */
        $memcacheConfig = $this->getConfig();
        $memcacheClient = new MemcachePoolObject($memcacheConfig->getHost(), $memcacheConfig->getPort());

        return $memcacheClient;
    }
}
