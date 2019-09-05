<?php

namespace SwooleKit\Cache\Pools;

use EasySwoole\Component\Pool\PoolObjectInterface;
use SwooleKit\Cache\Memcache\MemcacheClient;

/**
 * Memcache Pool Client
 * Class MemcachePoolObject
 * @package SwooleKit\Cache\Pools
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