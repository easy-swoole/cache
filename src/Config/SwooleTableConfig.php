<?php

namespace EasySwoole\Cache\Config;

use EasySwoole\Spl\SplBean;

/**
 * Class SwooleTableConfig
 * @package EasySwoole\Cache\Config
 */
class SwooleTableConfig extends SplBean
{
    protected $tableSize = 4096;          // 能容纳的总Key数量
    protected $maxKeySize = 512;          // Key支持的最大长度(字节)
    protected $maxValueSize = 4096;       // Value支持的最大长度(字节)
    protected $recycleInterval = 1000;    // 超时Key回收周期(ms)
    protected $conflictProportion = 0.2;  // 允许哈希冲突的最大比例

    /**
     * TableSize Getter
     * @return int
     */
    public function getTableSize(): int
    {
        return $this->tableSize;
    }

    /**
     * TableSize Setter
     * @param int $tableSize
     * @return SwooleTableConfig
     */
    public function setTableSize(int $tableSize): SwooleTableConfig
    {
        $this->tableSize = $tableSize;
        return $this;
    }

    /**
     * MaxKeySize Getter
     * @return int
     */
    public function getMaxKeySize(): int
    {
        return $this->maxKeySize;
    }

    /**
     * MaxKeySize Setter
     * @param int $maxKeySize
     * @return SwooleTableConfig
     */
    public function setMaxKeySize(int $maxKeySize): SwooleTableConfig
    {
        $this->maxKeySize = $maxKeySize;
        return $this;
    }

    /**
     * MaxValueSize Getter
     * @return int
     */
    public function getMaxValueSize(): int
    {
        return $this->maxValueSize;
    }

    /**
     * MaxValueSize Setter
     * @param int $maxValueSize
     * @return SwooleTableConfig
     */
    public function setMaxValueSize(int $maxValueSize): SwooleTableConfig
    {
        $this->maxValueSize = $maxValueSize;
        return $this;
    }

    /**
     * RecycleInterval Getter
     * @return int
     */
    public function getRecycleInterval(): int
    {
        return $this->recycleInterval;
    }

    /**
     * RecycleInterval Setter
     * @param int $recycleInterval
     * @return SwooleTableConfig
     */
    public function setRecycleInterval(int $recycleInterval): SwooleTableConfig
    {
        $this->recycleInterval = $recycleInterval;
        return $this;
    }

    /**
     * ConflictProportion Getter
     * @return float
     */
    public function getConflictProportion(): float
    {
        return $this->conflictProportion;
    }

    /**
     * ConflictProportion Setter
     * @param float $conflictProportion
     * @return SwooleTableConfig
     */
    public function setConflictProportion(float $conflictProportion): SwooleTableConfig
    {
        $this->conflictProportion = $conflictProportion;
        return $this;
    }

}