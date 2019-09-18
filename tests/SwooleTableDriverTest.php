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
use EasySwoole\Cache\Config\SwooleTableConfig;
use EasySwoole\Cache\Drivers\SwooleTable;
use PHPUnit\Framework\TestCase;

class SwooleTableDriverTest extends TestCase
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
        // 可以配置Table相关的一些配置项
        $swooleTableDriver = new SwooleTable((new SwooleTableConfig([

            'tableSize'          => 4096,  // 能容纳的总Key数量(由于哈希冲突，实际储存量会比该值小一点)
            'maxKeySize'         => 512,   // Key支持的最大长度(字节)
            'maxValueSize'       => 4096,  // Value支持的最大长度(字节)
            'recycleInterval'    => 1000,  // 超时Key回收周期(ms)
            'conflictProportion' => 0.2,   // 允许哈希冲突的最大比例

        ])));

        Cache::instance()->addDriver($swooleTableDriver, 'swTable');
        $this->cache = Cache::instance()->getDriver('swTable');
    }

    public function testSet()
    {
        $this->cache->set('key', 1);
        $this->assertEquals([
            'key'       => 'key',
            'value'     => 1,
            'expire'    => 0,
            'serialize' => 0,
        ], $this->cache->get('key'));
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
