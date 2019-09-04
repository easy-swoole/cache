<?php

namespace SwooleKit\Cache\Drivers;

use SwooleKit\Cache\Config\FileConfig;

/**
 * 文件缓存
 * Class File
 * @package Drivers
 */
class File extends AbstractDriver
{
    protected $cachePath;
    protected $cachePrefix;
    protected $defaultExpire;

    /**
     * File constructor.
     * @param FileConfig $cacheConfig
     */
    function __construct(FileConfig $cacheConfig)
    {
        $this->cachePath = $cacheConfig->getCachePath();
        $this->cachePrefix = $cacheConfig->getCachePrefix();
        $this->defaultExpire = $cacheConfig->getDefaultExpire();

        clearstatcache(true);
        if (!file_exists($this->cachePath)) {
            $status = \EasySwoole\Utility\File::createDirectory($this->cachePath);
        }

    }

}