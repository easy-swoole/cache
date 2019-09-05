<?php

namespace SwooleKit\Cache\Drivers;

use Opis\Closure\SerializableClosure;
use Psr\SimpleCache\CacheInterface;
use function serialize;
use function unserialize;

/**
 * 抽象驱动类
 * Class AbstractDriver
 * @package SwooleKit\Cache\Drivers
 */
abstract class AbstractDriver implements CacheInterface
{

    /**
     * 获取缓存
     * @param string $key
     * @param null $default
     * @return mixed
     */
    abstract public function get($key, $default = null);

    /**
     * 设置缓存
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return bool
     */
    abstract public function set($key, $value, $ttl = null);

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    abstract public function delete($key);

    /**
     * 清空缓存
     * @return bool
     */
    abstract public function clear();

    /**
     * 缓存是否存在
     * @param string $key
     * @return bool
     */
    abstract public function has($key);

    /**
     * 批量获取缓存
     * @param iterable $keys
     * @param null $default
     * @return array|iterable
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * 批量设置缓存
     * @param iterable $values
     * @param null $ttl
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $val) {
            $result = $this->set($key, $val, $ttl);
            if (false === $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 批量删除缓存
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $result = $this->delete($key);
            if (false === $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 序列化数据
     * @param $data
     * @return string
     */
    protected function serialize($data): string
    {
        $serialize = $this->options['serialize'][0] ?? function ($data) {
                SerializableClosure::enterContext();
                SerializableClosure::wrapClosures($data);
                $data = serialize($data);
                SerializableClosure::exitContext();
                return $data;
            };
        return $serialize($data);
    }

    /**
     * 反序列化数据
     * @param string $data
     * @return mixed
     */
    protected function unserialize(string $data)
    {
        $unserialize = $this->options['serialize'][1] ?? function ($data) {
                SerializableClosure::enterContext();
                $data = unserialize($data);
                SerializableClosure::unwrapClosures($data);
                SerializableClosure::exitContext();
                return $data;
            };
        return $unserialize($data);
    }
}