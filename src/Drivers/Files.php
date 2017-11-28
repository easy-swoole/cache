<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace easySwoole\Cache\Drivers;

/**
 * 文件缓存驱动
 * Class Files
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\Cache\Drivers
 */
class Files extends AbstractDriver
{
    protected $savePath;
    protected $timeOut;

    /**
     * Files constructor.
     * @param bool|string $savePath
     */
    function __construct($savePath = false)
    {
        if (!$savePath) {
            $this->savePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        } else {
            $this->setCachePath($savePath);
        }
    }

    /**
     * 设置缓存目录
     * @param $savePath
     * @author : evalor <master@evalor.cn>
     */
    function setCachePath($savePath)
    {
        $savePath = rtrim($savePath, '\x09\x20\x5C\2F') . DIRECTORY_SEPARATOR;
        $setPath = $this->initCachePath($savePath);
        $this->savePath = $setPath ? $savePath : sys_get_temp_dir() . DIRECTORY_SEPARATOR;
    }

    /**
     * 准备缓存文件目录
     * @param $savePath
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    protected function initCachePath($savePath)
    {
        if (!is_dir($savePath)) {
            if (!mkdir($savePath, 0755, true)) return false;
        }
        if (!is_writable($savePath)) return false;
        return true;
    }

    /**
     * 删除缓存文件
     * @param $filePath
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    protected function deleteCacheFile($filePath)
    {
        return is_file($filePath) && unlink($filePath);
    }

    /**
     * 生成文件路径
     * @param $key
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    protected function makeFilePath($key)
    {
        return $this->savePath . md5($key) . '.php';
    }

    /**
     * 文件上锁
     * @param $fileStream
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    protected function lock($fileStream)
    {
        return flock($fileStream, LOCK_EX);
    }

    /**
     * 文件解锁
     * @param $fileStream
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    protected function unlock($fileStream)
    {
        return flock($fileStream, LOCK_UN);
    }

    /**
     * 设置缓存
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) $ttl = 0;
        $cacheFile = $this->makeFilePath($key);
        $value = $this->pack($value);
        $data = "<?php\n//" . sprintf('%012d', $ttl) . "\n exit();?>\n" . $value;
        $fileStream = fopen($cacheFile, 'w');
        $this->lock($fileStream);
        $retval = fwrite($fileStream, $data);
        $this->unlock($fileStream);
        fclose($fileStream);
        if ($retval) clearstatcache();
        return $retval !== false;
    }

    /**
     * 获取缓存
     * @param string $key
     * @param null $default
     * @author : evalor <master@evalor.cn>
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $cacheFile = $this->makeFilePath($key);
        if (!is_file($cacheFile)) return $default;
        $content = file_get_contents($cacheFile);
        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if (0 != $expire && time() > filemtime($cacheFile) + $expire) {
                $this->deleteCacheFile($cacheFile);
                return $default;
            }
            return $this->unpack(substr($content, 32));
        } else {
            return $default;
        }
    }

    /**
     * 删除缓存
     * @param string $key
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function delete($key)
    {
        $cacheFile = $this->makeFilePath($key);
        return $this->deleteCacheFile($cacheFile);
    }

    /**
     * 清理缓存
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function clear()
    {
        $files = (array)glob($this->savePath . '*');
        foreach ($files as $path) {
            if (is_dir($path)) {
                $matches = glob($path . '/*.php');
                if (is_array($matches)) {
                    array_map('unlink', $matches);
                }
                rmdir($path);
            } else {
                unlink($path);
            }
        }
        return true;
    }
}