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

    protected $host   = '127.0.0.1';
    protected $port   = 6379;
    protected $auth   = null;
    protected $prefix = 'cache:';
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
     * @return $this
     */
    function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * 设置RedisPort
     * @param string|int $port
     * @author : evalor <master@evalor.cn>
     * @return $this
     */
    function setPort($port)
    {
        $this->port = intval($port);
        return $this;
    }

    /**
     * 设置RedisAuthPass
     * @param string $password
     * @author : evalor <master@evalor.cn>
     * @return $this
     */
    function setPassword($password)
    {
        $this->auth = (string)$password;
        return $this;
    }

    /**
     * 设置缓存前缀
     * @param string $prefix
     * @author : evalor <master@evalor.cn>
     * @return $this
     */
    function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
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
        try {
            $value = $this->handler->get($this->prefix . $key);
        } catch (\Exception $exception) {
            $this->connect();
            $value = $this->handler->get($this->prefix . $key);
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
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) $ttl = 0;
        $value = $this->pack($value);
        if (is_int($ttl) && $ttl) {
            try {
                $result = $this->handler->setex($this->prefix . $key, $ttl, $value);
            } catch (\Exception $exception) {
                $this->connect();
                $result = $this->handler->setex($this->prefix . $key, $ttl, $value);
            }
        } else {
            try {
                $result = $this->handler->set($this->prefix . $key, $value);
            } catch (\Exception $exception) {
                $this->connect();
                $result = $this->handler->set($this->prefix . $key, $value);
            }
        }
        return $result;
    }

    /**
     * 删除缓存
     * @param string $key
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    public function delete($key)
    {
        try {
            $this->handler->delete($this->prefix . $key);
        } catch (\Exception $exception) {
            $this->connect();
            $this->handler->delete($this->prefix . $key);
        }
        return true;
    }

    /**
     * 清空缓存
     * @author : evalor <master@evalor.cn>
     */
    public function clear()
    {
        try {
            $this->handler->delete($this->handler->keys($this->prefix . '*'));
        } catch (\Exception $exception) {
            $this->connect();
            $this->handler->delete($this->handler->keys($this->prefix . '*'));
        }
    }
}