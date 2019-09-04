<?php


namespace SwooleKit\Cache\Memcache;

use Swoole\Coroutine\Client as CoroutineClient;
use SwooleKit\Cache\Exception\CacheException;

/**
 * Memcache客户端
 * Class Memcache
 * @package Memcache
 */
class MemcacheClient
{
    /**
     * 协程客户端
     * @var CoroutineClient
     */
    protected $client;

    protected $memcacheHost;
    protected $memcachePort;

    protected $resultCode;
    protected $resultMessage;

    /**
     * Memcache constructor.
     * @param string $memcacheHost
     * @param int $memcachePort
     */
    function __construct($memcacheHost = '127.0.0.1', $memcachePort = 11211)
    {
        $this->memcacheHost = $memcacheHost;
        $this->memcachePort = $memcachePort;
    }

    /**
     * 连接客户端
     * @param null|integer $timeout
     * @return bool
     * @throws CacheException
     */
    function connect($timeout = null)
    {
        // 如果当前没有客户端则创建一个客户端
        if (!$this->client instanceof CoroutineClient) {

            $this->client = new CoroutineClient(SWOOLE_TCP);
            $this->client->set([
                'open_length_check'     => 1,
                'package_length_offset' => 8,
                'package_body_offset'   => 24,
                'package_length_type'   => 'N',
                'package_max_length'    => 1024 * 1024 * 1,
            ]);

        }

        // 如果当前客户端没有连接则进行连接
        if (!$this->client->isConnected()) {
            $connected = $this->client->connect($this->memcacheHost, $this->memcachePort, $timeout);
            if (!$connected) {
                $connectStr = "memcache://{$this->memcacheHost}:{$this->memcachePort}";
                throw new CacheException("Connect to Memcache {$connectStr} failed: {$this->client->errMsg}");
            }
        }

        return (bool)$this->client->isConnected();
    }

    /**
     * 发送一个原始命令
     * @param Protocol $protocol
     * @param int|null $timeout
     * @return Protocol
     * @throws CacheException
     */
    function sendCommand(Protocol $protocol, int $timeout = null): Protocol
    {
        if ($this->connect()) {
            $this->client->send($protocol->__toString());
            $binaryPackage = $this->client->recv($timeout);
            if ($binaryPackage && $binaryPackage !== '') {

                $resPack = new Protocol;
                $resPack->unpack($binaryPackage);

                $this->resultCode = $resPack->getStatus();
                if ($this->resultCode !== Status::STAT_NO_ERROR) {
                    $this->resultMessage = $resPack->getValue();
                }

                return $resPack;

            }
        }

        $connectStr = "memcache://{$this->memcacheHost}:{$this->memcachePort}";
        throw new CacheException("Send to Memcache {$connectStr} Failed: {$this->client->errMsg}");
    }

}