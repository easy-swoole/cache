<?php

namespace EasySwoole\Cache\Pools;

use EasySwoole\Component\Pool\PoolObjectInterface;
use EasySwoole\Cache\Memcache\MemcacheClient;

/**
 * Memcache Pool Client
 * Class MemcachePoolObject
 * @package EasySwoole\Cache\Pools
 */
class MemcachePoolObject extends MemcacheClient implements PoolObjectInterface
{
    function gc()
    {

    }

    function objectRestore()
    {

    }

    function beforeUse(): bool
    {
        return true;
    }

}