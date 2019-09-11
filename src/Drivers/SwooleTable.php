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

use EasySwoole\Cache\Config\SwooleTableConfig;
use EasySwoole\Component\Timer;
use EasySwoole\Utility\Random;
use Swoole\Table;

/**
 * SwooleTable缓存
 * Class Swoole.
 */
class SwooleTable extends AbstractDriver
{
    protected $tableName;
    protected $swooleConfig;
    protected $tableStructure;

    /** @var Table */
    protected $table;
    protected $timer;

    /**
     * Swoole constructor.
     * @param SwooleTableConfig|null $swooleConfig
     */
    public function __construct(SwooleTableConfig $swooleConfig = null)
    {
        $this->tableName = Random::character(32);
        if (!($swooleConfig instanceof SwooleTableConfig)) {
            $swooleConfig = new SwooleTableConfig();
        }

        $this->swooleConfig = $swooleConfig;

        // 定义储存表的结构
        $this->tableStructure = [
            'key'       => ['type' => Table::TYPE_STRING, 'size' => $swooleConfig->getMaxKeySize()],
            'value'     => ['type' => Table::TYPE_STRING, 'size' => $swooleConfig->getMaxValueSize()],
            'expire'    => ['type' => Table::TYPE_INT, 'size' => 4],
            'serialize' => ['type' => Table::TYPE_INT, 'size' => 1],
        ];

        // 创建数据存储表
        $this->initSwooleTable();
        $this->initRecycleTimer($swooleConfig->getRecycleInterval());
    }

    /**
     * 初始化数据表.
     * @return mixed
     */
    private function initSwooleTable()
    {
        // 如果表已存在直接释放表
        if ($this->table instanceof Table) {
            $this->table->destroy();
            unset($this->table);
        }

        // 创建一个新实例并进行初始化
        $this->table = new Table($this->swooleConfig->getTableSize(), $this->swooleConfig->getConflictProportion());
        foreach ($this->tableStructure as $columnName => $columnStruct) {
            $this->table->column($columnName, $columnStruct['type'], $columnStruct['size']);
        }

        return $this->table->create();
    }

    /**
     * 周期性回收.
     * @param int $interval
     */
    private function initRecycleTimer($interval = 1000)
    {
        $this->timer = Timer::getInstance()->loop($interval, function () {
            $currentTime = time();
            foreach ($this->table as $cacheItem) {
                if ($cacheItem['expire'] !== 0 && $cacheItem['expire'] < $currentTime) {
                    $this->table->del($cacheItem['key']);
                }
            }
        });
    }

    /**
     * 获取一个值
     * @param  string $key
     * @param  null   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = $this->table->get($key);
        if ($value !== false) {
            return $value['serialize'] === 1 ? $this->unserialize($value['value']) : $value;
        }

        return $default;
    }

    /**
     * 设置一个值
     * @param  string     $key
     * @param  mixed      $value
     * @param  null       $ttl
     * @return bool|mixed
     */
    public function set($key, $value, $ttl = null)
    {
        list($isSerialize, $setValue) = $this->processValue($value);

        return $this->table->set($key, [
            'key'       => $key,
            'value'     => $setValue,
            'expire'    => is_null($ttl) ? 0 : intval($ttl) + time(),
            'serialize' => $isSerialize,
        ]);
    }

    /**
     * 删除一个值
     * @param  string     $key
     * @return bool|mixed
     */
    public function delete($key)
    {
        return $this->table->del($key);
    }

    /**
     * 清空并重建表.
     * @return bool|mixed
     */
    public function clear()
    {
        return $this->initSwooleTable();
    }

    /**
     * 值是否存在.
     * @param  string     $key
     * @return bool|mixed
     */
    public function has($key)
    {
        return $this->table->exist($key);
    }

    /**
     * 缓存值序列化.
     * @param  mixed $value
     * @return array
     */
    private function processValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return [1, $this->serialize($value)];
        }

        return [0, $value];
    }
}
