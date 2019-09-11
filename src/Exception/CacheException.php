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

namespace EasySwoole\Cache\Exception;

use Exception;
use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * 缓存异常
 * Class CacheException.
 */
class CacheException extends Exception implements PsrCacheException
{
}
