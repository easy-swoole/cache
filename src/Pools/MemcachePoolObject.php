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

use EasySwoole\Cache\Memcache\MemcacheClient;
use EasySwoole\Component\Pool\PoolObjectInterface;

/**
 * Memcache Pool Client
 * Class MemcachePoolObject.
 */
class MemcachePoolObject extends MemcacheClient implements PoolObjectInterface
{
    public function gc()
    {
    }

    public function objectRestore()
    {
    }

    public function beforeUse(): bool
    {
        return true;
    }
}
