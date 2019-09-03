<?php

namespace SwooleKit\Cache\Exception;

use \Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * 无效参数异常
 * Class InvalidArgumentException
 * @package Exception
 */
class InvalidArgumentException extends CacheException implements PsrInvalidArgumentException
{

}