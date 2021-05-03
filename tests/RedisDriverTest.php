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
use EasySwoole\Cache\Drivers\Redis;
use EasySwoole\RedisPool\Pool;
use PHPUnit\Framework\TestCase;

class RedisDriverTest extends TestCase
{
    private static $cache;

    public static function setUpBeforeClass(): void
    {
        $pool = new Pool(new \EasySwoole\Redis\Config\RedisConfig([
            'host' => '127.0.0.1',
            'port' => 6379
        ]));
        $redisDriver = new Redis($pool);
        Cache::instance()->addDriver($redisDriver, 'redis');
        self::$cache = Cache::instance()->getDriver('redis');
    }


    public function testSet()
    {
        self::$cache->set('key', 1);
        $this->assertEquals(1, self::$cache->get('key'));
    }

    public function testGetWithTTL()
    {
        self::$cache->set('keyGetWithTTL', 2, 1);
        $this->assertEquals(2, self::$cache->get('keyGetWithTTL'));
        sleep(2);
        $this->assertEquals('', self::$cache->get('keyGetWithTTL'));
    }

    public function testInc()
    {
        Cache::set('inc', 1);
        Cache::inc('inc');
        $this->assertEquals(2, Cache::get('inc'));
    }

    public function testDec()
    {
        Cache::set('dec', 2);
        Cache::dec('dec');
        $this->assertEquals(1, Cache::get('dec'));
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

    public function testSetMultiple()
    {
        Cache::setMultiple([
            'set1' => 1,
            'set2' => 2,
        ], 10);
        $this->assertEquals(['set1' => 1], Cache::getMultiple(['set1'], 0));
        $this->assertEquals(['set1' => 1, 'set2' => 2], Cache::getMultiple(['set1', 'set2'], 0));
        $this->assertEquals(['set3' => 0], Cache::getMultiple(['set3'], 0));
    }

    public function testDeleteMultiple()
    {
        Cache::setMultiple([
            'set4' => 4,
            'set5' => 5,
        ]);
        $this->assertEquals(['set4' => 4, 'set5' => 5], Cache::getMultiple(['set4', 'set5'], 0));
        Cache::deleteMultiple(['set4', 'set5']);
        $this->assertEquals(['set4' => 0, 'ser5' => 0], Cache::getMultiple(['set4', 'ser5']));
    }
}
