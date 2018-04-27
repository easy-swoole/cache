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

namespace easySwoole\Cache\Connector;

use easySwoole\Cache\Exception\CacheException;

/**
 * Class Files.
 * @author : evalor <master@evalor.cn>
 */
class Files extends AbstractCache
{
    protected $options = [
        'expire'        => 0,     // 缓存过期时间
        'cache_subdir'  => true,  // 开启子目录存放
        'prefix'        => '',    // 缓存文件后缀名
        'path'          => '',    // 缓存文件储存路径
        'hash_type'     => 'md5', // 文件名的哈希方式
        'data_compress' => false, // 启用缓存内容压缩
        'thread_safe'   => false, // 线程安全模式
        'lock_timeout'  => 3000,  // 文件最长锁定时间(ms)
    ];

    protected $expire;

    /**
     * Files constructor.
     * @param  array          $options
     * @throws CacheException
     */
    public function __construct($options = [])
    {
        $DS = DIRECTORY_SEPARATOR;
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        if ($this->options['path'] == '') {
            $this->options['path'] = sys_get_temp_dir().$DS;
        }
        $this->options['path'] = rtrim($this->options['path'], $DS).$DS.'esCache'.$DS;
        $this->init();
    }

    /**
     * Init cache file path.
     * @author : evalor <master@evalor.cn>
     * @throws CacheException
     * @return bool
     */
    private function init()
    {
        if (!is_dir($this->options['path'])) {
            if (mkdir($this->options['path'], 0755, true) && chmod($this->options['path'], 0755)) {
                return true;
            }
        }

        if (!is_writeable($this->options['path'])) {
            throw new CacheException('Cache Path: '.$this->options['path'].' is not writable');
        }

        return false;
    }

    /**
     * Get the full path of the cache file.
     * @param $name
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    private function getCacheFileName($name)
    {
        $name = hash($this->options['hash_type'], $name);

        if ($this->options['cache_subdir']) {
            $name = substr($name, 0, 2).DIRECTORY_SEPARATOR.substr($name, 2);
        }

        if ($this->options['prefix']) {
            $name = $this->options['prefix'].DIRECTORY_SEPARATOR.$name;
        }

        $filename = $this->options['path'].$name.'.php';
        $dir      = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $filename;
    }

    /**
     * Delete cache file.
     * @param $path
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }

    /**
     * Read the contents of the file.
     * @param $name
     * @author : evalor <master@evalor.cn>
     * @return bool|string
     */
    private function getContent($name)
    {
        $filename = $this->getCacheFileName($name);
        if (!is_file($filename)) {
            return false;
        }

        if ($this->options['thread_safe']) {
            $fd = fopen($filename, 'r+');
            flock($fd, LOCK_SH);
            $fc = fread($fd, filesize($filename));
            fclose($fd);

            return $fc;
        } else {
            return file_get_contents($filename);
        }
    }

    /**
     * Write content into a file.
     * @param $name
     * @param $content
     * @author : evalor <master@evalor.cn>
     * @return bool|int
     */
    private function setContent($name, $content)
    {
        $filename = $this->getCacheFileName($name);

        if ($this->options['thread_safe']) {
            $fd = fopen($filename, 'w+');
            flock($fd, LOCK_EX);
            $fr = fwrite($fd, $content);
            fclose($fd);

            return $fr;
        } else {
            return file_put_contents($filename, $content);
        }
    }

    /**
     * Fetches a value from the cache.
     * @param string $name    The name of the item in store.
     * @param mixed  $default Default value to return if the key does not exist.
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $filename = $this->getCacheFileName($name);
        if (!is_file($filename)) {
            return $default;
        }

        $content      = $this->getContent($name);
        $this->expire = null;

        if ($content !== false) {
            $expire = (int) substr($content, 8, 12);

            if (0 != $expire && time() > filemtime($filename) + $expire) {
                $this->unlink($filename);

                return $default;
            }

            $this->expire = $expire;
            $content      = substr($content, 32);

            // If data compression is enable
            if ($this->options['data_compress'] && function_exists('gzcompress')) {
                $content = gzuncompress($content);
            }

            return $this->unPackData($content);
        } else {
            return $default;
        }
    }

    /**
     * Persists data in the cache, uniquely referenced by a name with an optional expiration TTL time.
     * @param string                 $name  The name of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function set($name, $value, $ttl = null)
    {
        if (is_null($ttl)) {
            $ttl = $this->options['expire'];
        }
        $ttl  = $this->getExpireTime($ttl);
        $data = $this->packData($value);

        // If data compression is enable
        if ($this->options['data_compress'] && function_exists('gzcompress')) {
            $data = gzcompress($data, 3);
        }

        $data   = "<?php\n//".sprintf('%012d', $ttl)."\n exit();?>\n".$data;
        $result = $this->setContent($name, $data);

        return boolval($result);
    }

    /**
     * Delete an item from the cache by its unique key.
     * @param string $name The name of the item in store.
     * @author : evalor <master@evalor.cn>
     * @return bool True on success and false on failure.
     */
    public function delete($name)
    {
        return $this->unlink($this->getCacheFileName($name));
    }

    /**
     * Determines whether an item is present in the cache.
     * @param string $name The name of the item in store.
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function has($name)
    {
        return is_file($this->getCacheFileName($name));
    }

    /**
     * Wipes clean the entire cache's keys.
     * @author : evalor <master@evalor.cn>
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        $cachePath   = $this->options['path'];
        $cachePrefix = $this->options['prefix'] ? $this->options['prefix'].DIRECTORY_SEPARATOR : '';
        $files       = (array) glob($cachePath.$cachePrefix.'*');

        foreach ($files as $file) {
            if (is_dir($file)) {
                $matches = glob($file.'/*.php');
                if (is_array($matches)) {
                    array_map('unlink', $matches);
                }
                rmdir($file);
            } else {
                $this->unlink($file);
            }
        }

        return true;
    }

    /**
     * Fetches a value from the cache and delete it.
     * @param  string $name    The name of the item in store.
     * @param  mixed  $default Default value to return if the key does not exist.
     * @return mixed
     * @author : evalor <master@evalor.cn>
     */
    public function pull($name, $default = null)
    {
        $result = $this->get($name, false);
        if ($result) {
            $this->delete($name);

            return $result;
        } else {
            return $default;
        }
    }

    /**
     * If the name does not exist, insert value.
     * @param  string                 $name  The name of the item to store.
     * @param  mixed                  $value The value of the item to store, must be serializable.
     * @param  null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     * @return bool
     * @author : evalor <master@evalor.cn>
     */
    public function remember($name, $value, $ttl = null)
    {
        if (!$this->has($name)) {
            return $this->set($name, $value, $ttl);
        }

        return $this->get($name);
    }

    public function driver()
    {
        return true;
    }
}
