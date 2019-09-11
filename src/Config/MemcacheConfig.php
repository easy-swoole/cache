<?php

namespace EasySwoole\Cache\Config;

use EasySwoole\Component\Pool\PoolConf;

/**
 * Class MemcacheConfig
 * @package Config
 */
class MemcacheConfig extends PoolConf
{
    protected $host = '127.0.0.1';
    protected $port = 11211;

    /**
     * Host Getter
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Host Setter
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * Port Getter
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Port Setter
     * @param mixed $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }
}