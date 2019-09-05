<?php

use Swoole\Coroutine;

require_once 'vendor/autoload.php';

Coroutine::create(function () {
    $memcache = new \SwooleKit\Cache\Drivers\Memcache;
    $memcache->set('admin', 'adminTime' . time(), 5);
    $memcache->get('admin', 'default');
    $memcache->delete('admin');
    $memcache->clear();
    $memcache->has('admin');
    $memcache->setMultiple(['admin' => '111', 'xxx' => 222]);
    $memcache->getMultiple(['admin', 'xxx']);
    $memcache->deleteMultiple(['admin', 'xxx']);
});