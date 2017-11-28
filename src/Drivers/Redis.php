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
 * Redis缓存驱动
 * Class Redis
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\Cache\Drivers
 */
class Redis extends AbstractDriver
{

    protected $host      = '127.0.0.1';
    protected $port      = 6379;
    protected $select    = 0;
    protected $auth      = null;
    protected $prefix    = 'cache:';
    protected $reconnect = true;

    /* @var \Redis $handler */
    protected $handler = null;

    function __construct($host = null)
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if ($host) $this->host = $host;
    }

    /**
     * 设置RedisHost
     * @param string $host
     * @author : evalor <master@evalor.cn>
     */
    function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * 设置RedisPort
     * @param string|int $port
     * @author : evalor <master@evalor.cn>
     */
    function setPort($port)
    {
        $this->port = intval($port);
    }

    /**
     * 设置RedisAuthPass
     * @param string $password
     * @author : evalor <master@evalor.cn>
     */
    function setPassword($password)
    {
        $this->auth = (string)$password;
    }

    /**
     * 设置缓存前缀
     * @param string $prefix
     * @author : evalor <master@evalor.cn>
     */
    function setPrefix($prefix)
    {
        $this->prefix = (string)$prefix;
    }

    /**
     * 设置缓存使用的数据库序号
     * @param $dbNum
     * @author : evalor <master@evalor.cn>
     */
    function setDatabase($dbNum)
    {
        $this->select = intval($dbNum);
    }

    /**
     * 设置是否开启断线重连
     * @param $reconnect
     * @author : evalor <master@evalor.cn>
     */
    function setReconnect($reconnect)
    {
        if (is_bool($reconnect)) $this->reconnect = $reconnect;
    }

    /**
     * 获取缓存
     * @param string $key
     * @param null $default
     * @return mixed|null
     * @throws \Exception
     * @author : evalor <master@evalor.cn>
     */
    public function get($key, $default = null)
    {
        if ($this->handler === null) $this->connect();

        try {
            $value = $this->handler->get($this->prefix . $key);
        } catch (\Exception $exception) {
            if ($this->isBreak($exception)) {
                $this->connect();
                $value = $this->handler->get($this->prefix . $key);
            } else {
                throw $exception;
            }
        }

        if (is_null($value) || false === $value) {
            return $default;
        }

        return $this->unpack($value);
    }

    /**
     * 设置缓存
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return bool
     * @throws \Exception
     * @author : evalor <master@evalor.cn>
     */
    public function set($key, $value, $ttl = null)
    {
        if ($this->handler === null) $this->connect();

        if (!$ttl) $ttl = 0;
        $value = $this->pack($value);
        try {
            if (is_int($ttl) && $ttl) {
                $result = $this->handler->setex($this->prefix . $key, $ttl, $value);
            } else {
                $result = $this->handler->set($this->prefix . $key, $value);
            }
            return $result;
        } catch (\Exception $exception) {
            if ($this->isBreak($exception)) {
                $this->connect();
                if (is_int($ttl) && $ttl) {
                    $result = $this->handler->setex($this->prefix . $key, $ttl, $value);
                } else {
                    $result = $this->handler->set($this->prefix . $key, $value);
                }
                return $result;
            } else {
                throw $exception;
            }
        }
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     * @throws \Exception
     * @author : evalor <master@evalor.cn>
     */
    public function delete($key)
    {
        if ($this->handler === null) $this->connect();

        try {
            $this->handler->delete($this->prefix . $key);
        } catch (\Exception $exception) {
            if ($this->isBreak($exception)) {
                $this->handler->delete($this->prefix . $key);
            } else {
                throw $exception;
            }
        }
        return true;
    }

    /**
     * 清空缓存
     * @author : evalor <master@evalor.cn>
     */
    public function clear()
    {
        if ($this->handler === null) $this->connect();

        try {
            $this->handler->delete($this->handler->keys($this->prefix . '*'));
        } catch (\Exception $exception) {
            if ($this->isBreak($exception)) {
                $this->handler->delete($this->handler->keys($this->prefix . '*'));
            } else {
                throw $exception;
            }
        }
    }

    /**
     * 连接到Redis
     * @author : evalor <master@evalor.cn>
     */
    protected function connect()
    {
        $this->handler = new \Redis();
        $this->handler->connect($this->host, $this->port, 0);
        if ($this->auth) $this->handler->auth($this->auth);
        if ($this->select != 0) $this->handler->select($this->select);
    }

    /**
     * 检测是否断线
     * @param \Exception $e
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    protected function isBreak(\Exception $e)
    {
        if (!$this->reconnect) return false;
        if (!$e instanceof \RedisException) return false;
        $info = ['server went away'];
        $error = $e->getMessage();
        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }
}