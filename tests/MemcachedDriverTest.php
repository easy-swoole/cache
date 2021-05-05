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
use EasySwoole\Cache\Drivers\Memcache;
use EasySwoole\Memcache\Config;
use EasySwoole\MemcachePool\Pool;
use PHPUnit\Framework\TestCase;

class MemcachedDriverTest extends TestCase
{
    private static $cache;

    static function setUpBeforeClass(): void
    {
        $pool = (new Pool(new Config([
            'host' => '127.0.0.1'
        ])));
        $driver = new Memcache($pool);
        Cache::instance()->addDriver($driver, 'memcache');
        self::$cache = Cache::instance()->getDriver('memcache');
    }

    public function testGet()
    {
        $this->assertEquals('no', self::$cache->get('key1', 'no'));
    }

    public function testSet()
    {
        self::$cache->set('key', 1);
        $this->assertEquals(1, self::$cache->get('key'));
    }

    public function testHas()
    {
        Cache::set('has', 3);
        $this->assertEquals(true, Cache::has('has'));
        $this->assertEquals(false, Cache::has('has1'));
    }

    public function testDelete()
    {
        Cache::set('delete', 4);
        $this->assertEquals(4, Cache::get('delete'));
        Cache::delete('delete');
        $this->assertEquals(false, Cache::has('delete'));
    }
}
