<?php

namespace EasySwoole\Cache\Exception;

use Exception;
use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * 缓存异常
 * Class CacheException
 * @package EasySwoole\Cache\Exception
 */
class CacheException extends Exception implements PsrCacheException
{

}