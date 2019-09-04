<?php

namespace SwooleKit\Cache\Exception;

use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * 缓存异常
 * Class CacheException
 * @package SwooleKit\Cache\Exception
 */
class CacheException extends \Exception implements PsrCacheException
{

}