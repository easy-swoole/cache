<?php

namespace SwooleKit\Cache\Memcache;

use EasySwoole\Spl\SplEnum;

/**
 * Memcache Status
 * Class Status
 * @package Memcache
 */
class Status extends SplEnum
{

    const STAT_NO_ERROR = 0x0000;
    const STAT_KEY_NOTFOUND = 0x0001;
    const STAT_KEY_EXISTS = 0x0002;
    const STAT_VALUE_TOO_LARGE = 0x0003;
    const STAT_INVALID_ARGS = 0x0004;
    const STAT_ITEM_NOT_STORED = 0x0005;
    const STAT_INCR_DECR_INVALID = 0x0006;
    const STAT_V_BUCKET_INVALID = 0x0007;
    const STAT_AUTH_ERROR = 0x0008;
    const STAT_AUTH_CONTINUE = 0x0009;
    const STAT_UNKNOWN_COMMAND = 0x0081;
    const STAT_OUT_OF_MEMORY = 0x0082;
    const STAT_NOT_SUPPORTED = 0x0083;
    const STAT_INTERNAL_ERROR = 0x0084;
    const STAT_SERVER_BUSY = 0x0085;
    const STAT_TEMPORARY_FAILURE = 0x0086;

    /**
     * 转为错误信息
     * @param $code
     * @return mixed|string
     */
    public static function code2Tips($code)
    {
        $tips = [
            Status::STAT_NO_ERROR          => 'No error',
            Status::STAT_KEY_NOTFOUND      => 'Key not found',
            Status::STAT_KEY_EXISTS        => 'Key exists',
            Status::STAT_VALUE_TOO_LARGE   => 'Value too large',
            Status::STAT_INVALID_ARGS      => 'Invalid arguments',
            Status::STAT_ITEM_NOT_STORED   => 'Item not stored',
            Status::STAT_INCR_DECR_INVALID => 'Incr/Decr on non-numeric value',
            Status::STAT_V_BUCKET_INVALID  => 'The vbucket belongs to another server',
            Status::STAT_AUTH_ERROR        => 'Authentication error',
            Status::STAT_AUTH_CONTINUE     => 'Authentication continue',
            Status::STAT_UNKNOWN_COMMAND   => 'Unknown command',
            Status::STAT_OUT_OF_MEMORY     => 'Out of memory',
            Status::STAT_NOT_SUPPORTED     => 'Not supported',
            Status::STAT_INTERNAL_ERROR    => 'Internal error',
            Status::STAT_SERVER_BUSY       => 'Server Busy',
            Status::STAT_TEMPORARY_FAILURE => 'Temporary failure',
        ];

        return array_key_exists($code, $tips) ? $tips[$code] : 'Unknown Status: ' . $code;
    }
}