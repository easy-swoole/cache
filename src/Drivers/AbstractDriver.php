<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace easySwoole\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;

/**
 * Cache Driver
 * Class AbstractDriver
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\Cache\Drivers
 */
abstract class AbstractDriver implements CacheInterface
{
    /**
     * 打包缓存数据
     * @param $value
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    protected function pack($value)
    {
        return serialize($value);
    }

    /**
     * 解包缓存数据
     * @param $value
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    protected function unpack($value)
    {
        return unserialize($value);
    }

    /**
     * 批量获取缓存
     * @param iterable $keys
     * @param null $default
     * @author : evalor <master@evalor.cn>
     * @return bool|iterable
     */
    function getMultiple($keys, $default = null)
    {
        $values = array();
        foreach ($keys as $name) {
            array_push($values, $this->get($name, $default));
        }
        return true;
    }

    /**
     * 批量设置缓存
     * @param iterable $values
     * @param null $ttl
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    function setMultiple($values, $ttl = null)
    {
        foreach ($values as $name => $item) {
            $this->set($name, $item);
        }
        return true;
    }

    /**
     * 批量删除缓存
     * @param iterable $keys
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    function deleteMultiple($keys)
    {
        foreach ($keys as $item) {
            $this->delete($item);
        }
        return true;
    }

    /**
     * 缓存是否存在
     * @param string $key
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    function has($key)
    {
        return $this->get($key, null) !== null;
    }
}