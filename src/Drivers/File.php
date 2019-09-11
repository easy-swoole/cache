<?php

namespace EasySwoole\Cache\Drivers;

use Exception;
use EasySwoole\Cache\Config\FileConfig;

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
    protected $expire;

    /**
     * 构造函数
     * File constructor.
     * @param FileConfig $cacheConfig
     * @throws Exception
     */
    function __construct(FileConfig $cacheConfig = null)
    {
        if (!($cacheConfig instanceof FileConfig)) {
            $cacheConfig = new FileConfig;
        }

        $this->cachePath = $cacheConfig->getCachePath();
        $this->cachePrefix = $cacheConfig->getCachePrefix();
        $this->defaultExpire = $cacheConfig->getDefaultExpire();

        clearstatcache(true);
        if (substr($this->cachePath, -1) != DIRECTORY_SEPARATOR) {
            $this->cachePath .= DIRECTORY_SEPARATOR;
        }

        if (!file_exists($this->cachePath)) {
            if (!\EasySwoole\Utility\File::createDirectory($this->cachePath)) {
                throw new Exception('File Cache Driver: directory create failed!');
            }
        }
    }

    /**
     * 获取存储文件名
     * @param $name
     * @param bool $auto
     * @return string
     */
    protected function getCacheKey($name, $auto = false)
    {
        $name = md5($name);
        if ($this->cachePrefix) {
            $name = $this->cachePrefix . DIRECTORY_SEPARATOR . $name;
        }
        $filename = $this->cachePath . $name . '.php';
        $dir = dirname($filename);
        if ($auto && !is_dir($dir)) {
            \EasySwoole\Utility\File::createDirectory($dir);
        }
        return $filename;
    }

    /**
     * 写入缓存
     * @param string $key
     * @param mixed $value
     * @param null $expire
     * @return bool
     */
    public function set($key, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->defaultExpire;
        }
        $filename = $this->getCacheKey($key, true);
        $data = $this->serialize($value);
        $data = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
        $result = file_put_contents($filename, $data, LOCK_EX);
        if ($result) {
            clearstatcache();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 读取缓存
     * @param string $key
     * @param null $default
     * @return bool|mixed|string
     */
    public function get($key, $default = null)
    {
        $filename = $this->getCacheKey($key);
        if (!is_file($filename)) {
            return $default;
        }
        $content = file_get_contents($filename);
        $this->expire = null;
        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if (0 != $expire && time() > filemtime($filename) + $expire) {
                $this->unlink($filename);
                return $default;
            }
            $this->expire = $expire;
            $content = substr($content, 32);
            $content = $this->unserialize($content);
            return $content;
        } else {
            return $default;
        }
    }

    /**
     * 自增缓存（针对数值缓存）
     * @param $key
     * @param int $step
     * @return bool|int|mixed|string
     */
    public function inc($key, $step = 1)
    {
        if ($this->has($key)) {
            $value = $this->get($key) + $step;
            $expire = $this->expire;
        } else {
            $value = $step;
            $expire = 0;
        }
        return $this->set($key, $value, $expire) ? $value : false;
    }

    /**
     * 自减缓存（针对数值缓存）
     * @param $key
     * @param int $step
     * @return bool|int|mixed|string
     */
    public function dec($key, $step = 1)
    {
        if ($this->has($key)) {
            $value = $this->get($key) - $step;
            $expire = $this->expire;
        } else {
            $value = -$step;
            $expire = 0;
        }
        return $this->set($key, $value, $expire) ? $value : false;
    }

    /**
     * 判断缓存是否存在
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->get($key) ? true : false;
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $filename = $this->getCacheKey($key);
        return $this->unlink($filename);
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clear()
    {
        $files = (array)glob($this->cachePath . ($this->cachePrefix ? $this->cachePrefix . DIRECTORY_SEPARATOR : '') . '*');
        foreach ($files as $path) {
            if (is_dir($path)) {
                $matches = glob($path . '/*.php');
                if (is_array($matches)) {
                    array_map('unlink', $matches);
                }
            } else {
                unlink($path);
            }
        }
        return true;
    }

    /**
     * 判断文件是否存在后，删除
     * @param $path
     * @return bool
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }
}