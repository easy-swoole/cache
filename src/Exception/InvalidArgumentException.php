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

use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * 无效参数异常
 * Class InvalidArgumentException.
 */
class InvalidArgumentException extends CacheException implements PsrInvalidArgumentException
{
}
