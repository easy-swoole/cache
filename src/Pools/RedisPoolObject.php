<?php

namespace EasySwoole\Cache\Pools;

use EasySwoole\Component\Pool\PoolObjectInterface;
use Swoole\Coroutine\Redis;

/**
 * 协程Redis客户端
 * Class RedisPoolObject
 * @package EasySwoole\Cache\Pools
 */
class RedisPoolObject extends Redis implements PoolObjectInterface
{
    function gc()
    {
        $this->close();
    }

    function objectRestore()
    {

    }

    function beforeUse(): bool
    {
        return true;
    }

}