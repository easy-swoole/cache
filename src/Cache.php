<?php

namespace SwooleKit\Cache;

use SwooleKit\Cache\Drivers\AbstractDriver;
use SwooleKit\Cache\Drivers\File as FileDriver;
use SwooleKit\Cache\Exception\CacheException;

/**
 * 缓存管理器
 * Class Cache
 * @method static mixed get($key, $default = null)
 * @method static bool set($key, $value, $ttl = null)
 * @method static bool delete($key)
 * @method static bool clear()
 * @method static iterable getMultiple($keys, $default = null)
 * @method static bool setMultiple($values, $ttl = null)
 * @method static bool deleteMultiple($keys)
 * @method static bool has($key)
 * @package SwooleKit\Cache
 */
class Cache
{
    /**
     * 管理器实例
     * @var Cache
     */
    protected static $instance;

    /**
     * 缓存驱动
     * @var AbstractDriver[]
     */
    protected $drivers = [];

    /**
     * 获取缓存实例
     * @return Cache
     */
    public static function instance()
    {
        // 如果当前实例没有被注册则注册当前实例(单例)
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 添加驱动
     * @param AbstractDriver $driver
     * @param $driverName
     * @return bool
     * @throws CacheException
     */
    public function addDriver($driver, $driverName = 'default')
    {
        // 不允许注册相同名称的Driver (但可以注册default来覆盖默认驱动)
        if ($driverName != 'default' && array_key_exists($driverName, $this->drivers)) {
            throw new CacheException("Driver name {$driverName} has been used");
        }

        // 注册到当前缓存容器中
        if ($driver instanceof AbstractDriver) {
            $this->drivers[$driverName] = $driver;
            return true;
        }

        throw new CacheException("Driver name {$driverName} must be instance of AbstractDriver");
    }

    /**
     * 获取一个驱动类
     * @param string $driverName
     * @return AbstractDriver
     * @throws CacheException
     */
    public function getDriver($driverName = 'default')
    {
        // 如果要获取的驱动是默认驱动 且默认驱动尚未注册 则自动注册默认驱动
        if (!array_key_exists($driverName, $this->drivers)) {
            if ($driverName === 'default') {
                $this->drivers['default'] = new FileDriver;
                return $this->drivers['default'];
            }
            throw new CacheException("Driver name {$driverName} does not exist");
        }
        return $this->drivers[$driverName];
    }

    /**
     * 静态化调用
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws CacheException
     */
    public static function __callStatic($name, $arguments)
    {
        return self::instance()->getDriver('default')->$name(...$arguments);
    }
}