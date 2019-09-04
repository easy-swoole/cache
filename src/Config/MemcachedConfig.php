<?php

namespace SwooleKit\Cache\Config;

use EasySwoole\Component\Pool\PoolConf;

/**
 * Class MemcachedConfig
 * @package Config
 */
class MemcachedConfig extends PoolConf
{
    protected $host;
    protected $port;

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