<?php

namespace easySwoole\Cache;

use easySwoole\Cache\Connector\Files;

/**
 * easySwoole Cache Manager
 * Class Cache
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\Cache
 * @method static inc              ($key, $value = null)
 * @method static dec              ($key, $value = null)
 * @method static pull             ($key, $default = null)
 * @method static remember         ($key, $value, $ttl = null)
 * @method static get              ($key, $default = null)
 * @method static set              ($key, $value, $ttl = null)
 * @method static delete           ($key)
 * @method static clear            ()
 * @method static has              ($key)
 */
class Cache
{
    protected static $connector;

    /**
     * Cache handle initialize
     * Cache constructor.
     * @param $connector
     */
    static function init($connector = null)
    {
        if (is_null($connector)) $connector = new Files;
        self::$connector = $connector;
    }

    /**
     * Call connector method
     * @param $name
     * @param $arguments
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    static function __callStatic($name, $arguments)
    {
        if (!is_object(self::$connector)) self::init();
        return self::$connector->$name(...$arguments);
    }
}