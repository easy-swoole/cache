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

namespace EasySwoole\Cache\Config;

use EasySwoole\Component\Pool\PoolConf;

/**
 * Class MemcacheConfig.
 */
class MemcacheConfig extends PoolConf
{
    protected $host = '127.0.0.1';
    protected $port = 11211;

    /**
     * Host Getter.
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Host Setter.
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * Port Getter.
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Port Setter.
     * @param mixed $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }
}
