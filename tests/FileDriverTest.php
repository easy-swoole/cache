<?php
/**
 * Created by PhpStorm.
 * User: Manlin
 * Date: 2019/9/12
 * Time: 下午4:25
 */
namespace EasySwoole\Cache\Test;

use EasySwoole\Cache\Cache;
use EasySwoole\Cache\Config\FileConfig;
use EasySwoole\Cache\Drivers\File;
use PHPUnit\Framework\TestCase;

class FileDriverTest extends TestCase
{
    /**
     * FileDriverTest constructor.
     * @param null|string $name
     * @param array       $data
     * @param string      $dataName
     * @throws \EasySwoole\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $fileDriver = new File(new FileConfig([
            'cachePath' => sys_get_temp_dir(), // 缓存目录(默认为PHP系统缓存)
            'cachePrefix' => null, // 默认的缓存前缀 不同前缀分目录存放
            'defaultExpire' => 0 // 默认过期时间 设置为0永不过期
        ]));
        // 注册驱动时不指定驱动名称，则为注册default驱动
        // 仅default驱动可以多次注册，实际调用最后一次注册的default驱动
        // 其他驱动名称仅允许注册一次，不允许重复注册相同名称的驱动
        Cache::instance()->addDriver($fileDriver, 'default');

        // 从缓存管理器单例中获取任意名字的驱动，不指定名字为获取default驱动
        Cache::instance()->getDriver('default');
    }

    public function testSet()
    {
        Cache::set('key', 1);
        $this->assertEquals(1, Cache::get('key'));
    }

    public function testGetWithTTL()
    {
        Cache::set('keyGetWithTTL', 2, 1);
        $this->assertEquals(2, Cache::get('keyGetWithTTL'));
        sleep(2);
        $this->assertEquals('', Cache::get('keyGetWithTTL'));
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
            'set2' => 2
        ], 10);
        $this->assertEquals(['set1' => 1], Cache::getMultiple(['set1'], 0));
        $this->assertEquals(['set1' => 1, 'set2' => 2], Cache::getMultiple(['set1', 'set2'], 0));
        $this->assertEquals(['set3' => 0], Cache::getMultiple(['set3'], 0));
    }

    public function testDeleteMultiple()
    {
        Cache::setMultiple([
            'set4' => 4,
            'set5' => 5
        ]);
        $this->assertEquals(['set4' => 4, 'set5' => 5], Cache::getMultiple(['set4', 'set5'], 0));
        Cache::deleteMultiple(['set4', 'set5']);
        $this->assertEquals(['set4' => 0, 'ser5' => 0], Cache::getMultiple(['set4', 'ser5']));
    }
}
