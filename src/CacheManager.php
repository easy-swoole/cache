<?php

namespace SwooleKit\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

/**
 * 缓存管理器
 * Class CacheManager
 * @package SwooleKit\Cache
 */
class CacheManager
{
    public function getInstance()
    {

    }

    public function addDriver(PsrCacheInterface $cacheDriver, $driverName = 'default')
    {

    }

    public function getDriver($driverName = 'default')
    {

    }
}