<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace easySwoole\Cache;

use easySwoole\Cache\Drivers\Files;
use easySwoole\Cache\Drivers\Redis;
use easySwoole\Cache\Drivers\AbstractDriver;

/**
 * Class Cache
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\Cache
 * @method AbstractDriver clear() static 清空缓存
 * @method AbstractDriver delete($key) static 删除缓存
 * @method AbstractDriver has($key) static 缓存是否存在
 * @method AbstractDriver get($key, $default = null) static 获取缓存
 * @method AbstractDriver set($key, $value, $ttl = null) static 设置缓存
 * @method AbstractDriver getMultiple($keys, $default = null) static 批量设置
 * @method AbstractDriver setMultiple($values, $ttl = null) static 批量获取
 * @method AbstractDriver deleteMultiple($keys) static 批量删除
 */
class Cache
{
    protected static $handle = null;

    /**
     * 初始化缓存
     * @param Files|Redis $handle
     * @author : evalor <master@evalor.cn>
     */
    static function init($handle = null)
    {
        if ($handle === null) {
            self::$handle = new Files();
        } else {
            self::$handle = $handle;
        }
    }

    /**
     * 调用缓存驱动方法
     * @param $name
     * @param $arguments
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    static function __callStatic($name, $arguments)
    {
        if (self::$handle === null) self::init();
        return self::$handle->$name(...$arguments);
    }
}