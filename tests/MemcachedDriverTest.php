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

namespace EasySwoole\Cache\Test;

use EasySwoole\Cache\Cache;
use EasySwoole\Cache\Config\MemcacheConfig;
use EasySwoole\Cache\Drivers\Memcache;
use PHPUnit\Framework\TestCase;

class MemcachedDriverTest extends TestCase
{
    private $cache;

    /**
     * SwooleTableDriverTest constructor.
     * @param  null|string                                $name
     * @param  array                                      $data
     * @param  string                                     $dataName
     * @throws \EasySwoole\Cache\Exception\CacheException
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        // Memcache的Config可以同时配置链接池相关的设置项
        $memcacheDriver = new Memcache((new MemcacheConfig([

            // 链接配置项
            'host'              => '127.0.0.1',  // 默认是连接本地Memcache
            'port'              => 11211,        // 默认的端口

            // 链接池配置项
            'intervalCheckTime' => 30 * 1000,   // 池对象回收检测周期
            'maxIdleTime'       => 15,          // 连接最大空闲时间(超时释放)
            'maxObjectNum'      => 20,          // 池最大连接象数量
            'minObjectNum'      => 5,           // 保持的最小连接数量
            'getObjectTimeout'  => 3.0,          // 池为空时获取连接最大等待时间

        ])));

        Cache::instance()->addDriver($memcacheDriver, 'memcache');
        $this->cache = Cache::instance()->getDriver('memcache');
    }

    public function testSet()
    {
        $this->cache->set('key', 1);
        $this->assertEquals(1, $this->cache->get('key'));
    }

    public function testHas()
    {
        Cache::set('has', 3);
        $this->assertEquals(true, Cache::has('has'));
    }

    public function testDelete()
    {
        Cache::set('delete', 4);
        $this->assertEquals(4, Cache::get('delete'));
        Cache::delete('delete');
        $this->assertEquals(false, Cache::has('delete'));
    }
}
