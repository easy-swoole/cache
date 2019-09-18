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
use EasySwoole\Cache\Config\RedisConfig;
use EasySwoole\Cache\Drivers\Redis;
use PHPUnit\Framework\TestCase;

class RedisDriverTest extends TestCase
{
    private $cache;

    /**
     * FileDriverTest constructor.
     * @param  null|string                                $name
     * @param  array                                      $data
     * @param  string                                     $dataName
     * @throws \EasySwoole\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        // Redis的Config可以同时配置链接池相关的设置项
        $redisDriver = new Redis((new RedisConfig([

            // 链接配置项
            'db'                => 0,           // 有多个DB时可以选择使用的DB 默认选择0号DB
            'host'              => '127.0.0.1', // 默认是连接本地Redis
            'port'              => 6379,        // 默认的端口
            'auth'              => null,        // 默认没有密码
            'connectTimeout'    => 1,           // 连接到服务器的超时时间
            'execTimeout'       => 1,           // 执行操作的超时时间
            'reconnect'         => 3,           // 如果连接断开自动尝试x次重连

            // 链接池配置项
            'intervalCheckTime' => 30 * 1000,   // 池对象回收检测周期
            'maxIdleTime'       => 15,          // 连接最大空闲时间(超时释放)
            'maxObjectNum'      => 20,          // 池最大连接象数量
            'minObjectNum'      => 5,           // 保持的最小连接数量
            'getObjectTimeout'  => 3.0,          // 池为空时获取连接最大等待时间

        ])));

        Cache::instance()->addDriver($redisDriver, 'redis');
        $this->cache = Cache::instance()->getDriver('redis');
        parent::__construct($name, $data, $dataName);
    }

    public function testSet()
    {
        $this->cache->set('key', 1);
        $this->assertEquals(1, $this->cache->get('key'));
    }

    public function testGetWithTTL()
    {
        $this->cache->set('keyGetWithTTL', 2, 1);
        $this->assertEquals(2, $this->cache->get('keyGetWithTTL'));
        sleep(2);
        $this->assertEquals('', $this->cache->get('keyGetWithTTL'));
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
