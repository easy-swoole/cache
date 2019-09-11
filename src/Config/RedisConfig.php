<?php

namespace EasySwoole\Cache\Config;

use EasySwoole\Component\Pool\PoolConf;

/**
 * Class RedisConfig
 * @package EasySwoole\Cache\Config
 */
class RedisConfig extends PoolConf
{
    protected $db = 0;
    protected $host = '127.0.0.1';
    protected $port = 6379;
    protected $auth = null;
    protected $connectTimeout = 1;
    protected $execTimeout = 1;
    protected $reconnect = 3;
    protected $serialize = true;
    protected $compatibilityMode = true;

    /**
     * Db Getter
     * @return int
     */
    public function getDb(): int
    {
        return $this->db;
    }

    /**
     * Db Setter
     * @param int $db
     * @return RedisConfig
     */
    public function setDb(int $db): RedisConfig
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Host Getter
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Host Setter
     * @param string $host
     * @return RedisConfig
     */
    public function setHost(string $host): RedisConfig
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Port Getter
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Port Setter
     * @param int $port
     * @return RedisConfig
     */
    public function setPort(int $port): RedisConfig
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Auth Getter
     * @return null
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Auth Setter
     * @param null $auth
     * @return RedisConfig
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * ConnectTimeout Getter
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * ConnectTimeout Setter
     * @param int $connectTimeout
     * @return RedisConfig
     */
    public function setConnectTimeout(int $connectTimeout): RedisConfig
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * ExecTimeout Getter
     * @return int
     */
    public function getExecTimeout(): int
    {
        return $this->execTimeout;
    }

    /**
     * ExecTimeout Setter
     * @param int $execTimeout
     * @return RedisConfig
     */
    public function setExecTimeout(int $execTimeout): RedisConfig
    {
        $this->execTimeout = $execTimeout;
        return $this;
    }

    /**
     * Reconnect Getter
     * @return int
     */
    public function getReconnect(): int
    {
        return $this->reconnect;
    }

    /**
     * Reconnect Setter
     * @param int $reconnect
     * @return RedisConfig
     */
    public function setReconnect(int $reconnect): RedisConfig
    {
        $this->reconnect = $reconnect;
        return $this;
    }

    /**
     * Serialize Getter
     * @return bool
     */
    public function isSerialize(): bool
    {
        return $this->serialize;
    }

    /**
     * CompatibilityMode Getter
     * @return bool
     */
    public function isCompatibilityMode(): bool
    {
        return $this->compatibilityMode;
    }

}