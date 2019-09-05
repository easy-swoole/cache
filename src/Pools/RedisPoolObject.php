<?php

namespace SwooleKit\Cache\Pools;

use EasySwoole\Component\Pool\PoolObjectInterface;
use Swoole\Coroutine\Redis;

/**
 * 协程Redis客户端
 * Class RedisPoolObject
 * @package SwooleKit\Cache\Pools
 */
class RedisPoolObject extends Redis implements PoolObjectInterface
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