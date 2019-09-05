<?php

namespace SwooleKit\Cache\Drivers;

use EasySwoole\Component\Timer;
use EasySwoole\Utility\Random;
use Swoole\Table;
use SwooleKit\Cache\Config\SwooleConfig;

/**
 * SwooleTable缓存
 * Class Swoole
 * @package Drivers
 */
class Swoole extends AbstractDriver
{
    protected $tableName;
    protected $swooleConfig;
    protected $tableStructure;

    /** @var Table */
    protected $table;
    protected $timer;

    /**
     * Swoole constructor.
     * @param SwooleConfig|null $swooleConfig
     */
    function __construct(SwooleConfig $swooleConfig = null)
    {
        $this->tableName = Random::character(32);
        if (!($swooleConfig instanceof SwooleConfig)) {
            $swooleConfig = new SwooleConfig;
        }

        $this->swooleConfig = $swooleConfig;

        // 定义储存表的结构
        $this->tableStructure = [
            'key'    => ['type' => Table::TYPE_STRING, 'size' => $swooleConfig->getMaxKeySize()],
            'value'  => ['type' => Table::TYPE_STRING, 'size' => $swooleConfig->getMaxValueSize()],
            'expire' => ['type' => Table::TYPE_INT, 'size' => 4]
        ];

        // 创建数据存储表
        $this->initSwooleTable();
        $this->initRecycleTimer($swooleConfig->getRecycleInterval());
    }

    /**
     * 初始化数据表
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
     * 周期性回收
     * @param int $interval
     */
    private function initRecycleTimer($interval = 1000)
    {
        $this->timer = Timer::getInstance()->loop($interval, function () {
            $currentTime = time();
            foreach ($this->table as $cacheItem) {
                if ($cacheItem['expire'] < $currentTime) {
                    $this->table->del($cacheItem['key']);
                }
            }
        });
    }

    public function get($key, $default = null)
    {
        // TODO: Implement get() method.
    }

    public function set($key, $value, $ttl = null)
    {
        // TODO: Implement set() method.
    }

    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    /**
     * 清空并重建表
     * @return bool|mixed
     */
    public function clear()
    {
        return $this->initSwooleTable();
    }

    public function has($key)
    {
        // TODO: Implement has() method.
    }


}