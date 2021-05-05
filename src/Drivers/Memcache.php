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

namespace EasySwoole\Cache\Drivers;

use EasySwoole\Memcache\Opcode;
use EasySwoole\Memcache\Package;
use EasySwoole\Memcache\Status;
use EasySwoole\MemcachePool\Pool;
use EasySwoole\Pool\Exception\PoolEmpty;

/**
 * Memcache缓存
 * Class Memcached.
 */
class Memcache extends AbstractDriver
{

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * Memcache constructor.
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }


    /**
     * @param string $key
     * @param null $default
     * @return false|mixed
     * @throws PoolEmpty
     * @throws \Throwable
     */
    public function get($key, $default = null)
    {
        return $this->pool->invoke(function (\EasySwoole\Memcache\Memcache $memcache) use ($key, $default) {
            return $memcache->get($key) ?? $default;
        });
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws \Throwable
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->pool->invoke(function (\EasySwoole\Memcache\Memcache $memcache) use ($key, $value, $ttl) {
            return $memcache->set($key, $value, $ttl);
        });
    }

    /**
     * @param string $key
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws \Throwable
     */
    public function delete($key)
    {
        return $this->pool->invoke(function (\EasySwoole\Memcache\Memcache $memcache) use ($key) {
            return $memcache->delete($key);
        });
    }

    /**
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws \Throwable
     */
    public function clear()
    {
        return $this->pool->invoke(function (\EasySwoole\Memcache\Memcache $memcache) {
            return $memcache->flush();
        });
    }

    /**
     * @param string $key
     * @return bool|mixed
     * @throws PoolEmpty
     * @throws \Throwable
     */
    public function has($key)
    {
        return $this->pool->invoke(function (\EasySwoole\Memcache\Memcache $memcache) use ($key) {
            $reqPack = new Package(['opcode' => Opcode::OP_GET, 'key' => $key]);
            $resPack = $memcache->sendCommand($reqPack);

            if ($resPack->getStatus() === Status::STAT_KEY_NOTFOUND) {
                return false;
            }

            return true;
        });
    }
}
