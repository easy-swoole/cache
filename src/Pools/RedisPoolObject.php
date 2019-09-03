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
        // TODO: Implement gc() method.
    }

    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }

    function beforeUse(): bool
    {
        // TODO: Implement beforeUse() method.
    }

}