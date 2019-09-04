<?php

namespace SwooleKit\Cache\Config;

use EasySwoole\Component\Pool\PoolConf;

/**
 * Class RedisConfig
 * @package SwooleKit\Cache\Config
 */
class RedisConfig extends PoolConf
{
    protected $db = 0;
    protected $host = '127.0.0.1';
    protected $port = 6379;
    protected $auth = null;

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
     */
    public function setDb(int $db): void
    {
        $this->db = $db;
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
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
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
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
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
     */
    public function setAuth($auth): void
    {
        $this->auth = $auth;
    }
}