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

namespace easySwoole\Cache;

use easySwoole\Cache\Connector\Files;
use easySwoole\Cache\Connector\Memcache;
use easySwoole\Cache\Connector\Memcached;
use easySwoole\Cache\Connector\Redis;
use easySwoole\Cache\Exception\CacheException;

/**
 * easySwoole Cache Manager
 * Class Cache.
 * @author : evalor <master@evalor.cn>
 * @method static inc              ($key, $value = null)
 * @method static dec              ($key, $value = null)
 * @method static pull             ($key, $default = null)
 * @method static remember         ($key, $value, $ttl = null)
 * @method static get              ($key, $default = null)
 * @method static set              ($key, $value, $ttl = null)
 * @method static delete           ($key)
 * @method static clear            ()
 * @method static has              ($key)
 * @method static driver           ()
 */
class Cache
{
    protected static $connector;

    /**
     * Cache handle initialize
     * Cache constructor.
     * @param $connector
     * @throws CacheException
     */
    public static function init($connector = null)
    {
        $classMap  = [Files::class, Redis::class, Memcache::class, Memcached::class];
        $driverMap = ['files', 'redis', 'memcache', 'memcached'];

        if (is_null($connector)) {
            $connector = new Files();
        } elseif (is_array($connector)) {
            $driver = $connector['driver'];
            if (!in_array($driver, $driverMap)) {
                throw new CacheException('unknown cache driver: '.$driver);
            }
            $class     = 'easySwoole\\Cache\\Connector\\'.ucfirst($driver);
            $connector = new $class($connector);
        } elseif (is_object($connector)) {
            $className = get_class($connector);
            if (!in_array($className, $classMap)) {
                throw new CacheException('unknown cache driver: '.$className);
            }
        } else {
            throw new CacheException('cache driver options invalid');
        }

        self::$connector = $connector;
    }

    /**
     * Call connector method.
     * @param $name
     * @param $arguments
     * @author : evalor <master@evalor.cn>
     * @throws CacheException
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (!is_object(self::$connector)) {
            self::init();
        }

        return self::$connector->$name(...$arguments);
    }
}
