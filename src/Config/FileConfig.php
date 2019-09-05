<?php

namespace SwooleKit\Cache\Config;

use EasySwoole\Spl\SplBean;

/**
 * 文件缓存配置
 * Class FileConfig
 * @package Config
 */
class FileConfig extends SplBean
{
    protected $cachePath;
    protected $cachePrefix;
    protected $defaultExpire;

    function __construct(array $data = null, $autoCreateProperty = false)
    {
        $this->initDefaultConfig();
        parent::__construct($data, $autoCreateProperty);
    }

    /**
     * 设置默认的配置值
     * @return void
     */
    private function initDefaultConfig()
    {
        $this->cachePath = sys_get_temp_dir();
        $this->cachePrefix = null;
        $this->defaultExpire = 0;
    }

    /**
     * 获取缓存目录
     * @return mixed
     */
    public function getCachePath()
    {
        return $this->cachePath;
    }

    /**
     * 设置缓存目录
     * @param string $cachePath
     */
    public function setCachePath(string $cachePath): void
    {
        $this->cachePath = $cachePath;
    }

    /**
     * 获取缓存前缀
     * @return mixed
     */
    public function getCachePrefix()
    {
        return $this->cachePrefix;
    }

    /**
     * 设置缓存前缀
     * @param string $cachePrefix
     */
    public function setCachePrefix(string $cachePrefix): void
    {
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * 获取缓存超时
     * @return mixed
     */
    public function getDefaultExpire()
    {
        return $this->defaultExpire;
    }

    /**
     * 设置缓存超时
     * @param int $defaultExpire
     */
    public function setDefaultExpire(int $defaultExpire): void
    {
        $this->defaultExpire = $defaultExpire;
    }
}