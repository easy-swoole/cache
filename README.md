Coroutine Cache
======

一个轻量级(PSR-16规范)的缓存实现，目前已支持 File Redis Memcached SwooleTable 四种储存模式

### 安装

> composer require swoole-kit/cache

### 快速开始

支持开箱即用的快速缓存，默认使用File驱动，该驱动可以通用协程与非协程模式，所有内置驱动均以PSR16规范实现，静态调用时默认均为调用default驱动，当没有手动注册default驱动时，默认的default驱动为File驱动

```php

use EasySwoole\Cache\Cache;

$ttl = 10;  // 单位秒
$cacheKey = 'cacheKey';
$cacheValue = 'cacheValue';
$defaultValue = 'defaultValue';

Cache::clear();
Cache::has($cacheKey);
Cache::delete($cacheKey);
Cache::set($cacheKey, $cacheValue, $ttl);
Cache::get($cacheKey, $defaultValue);
Cache::setMultiple(['CacheKey1' => 'CacheValue1',], $ttl);
Cache::getMultiple(['CacheKey1'], $defaultValue);
Cache::deleteMultiple(['CacheKey1']);

```

### 注册驱动

系统提供了四种默认驱动，除文件驱动可以在非Swoole环境使用，其他驱动因使用了Sw相关特性或客户端实现，必须在Swoole环境下使用

### 文件驱动

文件驱动是最洒脱(无任何依赖通杀所有环节)的驱动，性能较其他驱动相对来说比较低，这里演示文件驱动的注册方法，其他驱动的注册方法一致，只是配置项上会有区别

```php

// 请注意先Use要使用的驱动类和配置类
use EasySwoole\Cache\Config\FileConfig;
use EasySwoole\Cache\Drivers\File as FileDriver;

// 也可以不传入Config 自动使用下面的默认值
$fileDriver = new FileDriver((new FileConfig([
    'cachePath'     => sys_get_temp_dir(),   // 缓存目录(默认为PHP系统缓存)
    'cachePrefix'   => null, // 默认的缓存前缀 不同前缀分目录存放
    'defaultExpire' => 0,    // 默认过期时间 设置为0永不过期
])));

// 注册驱动时不指定驱动名称，则为注册default驱动
// 仅default驱动可以多次注册，实际调用最后一次注册的default驱动
// 其他驱动名称仅允许注册一次，不允许重复注册相同名称的驱动
Cache::instance()->addDriver($fileDriver,'default');

// 从缓存管理器单例中获取任意名字的驱动，不指定名字为获取default驱动
$cache = Cache::instance()->getDriver('default');

```

### Redis驱动

该驱动使用Swoole内置的Redis协程客户端，内部已实现自动连接池管理，可以在协程模式下安全的使用

> 注意: 当一次请求存在多个协程时(比如说手动create了协程)，则会为每一个协程都分配一个链接，Memcache驱动也一样，实际上不会影响使用，因为并没有用到事务相关特性:)，但需要注意避免创建大量嵌套协程导致瞬间取空连接池

```php

// 请注意先Use要使用的驱动类和配置类
use EasySwoole\Cache\Config\RedisConfig;
use EasySwoole\Cache\Drivers\Redis as RedisDriver;

// 需要协程环境(在EasySwoole框架内无需手动创建协程)
Coroutine::create(function () {

    // Redis的Config可以同时配置链接池相关的设置项
    $redisDriver = new RedisDriver((new RedisConfig([

        // 链接配置项
        'db'                => 0,           // 有多个DB时可以选择使用的DB 默认选择0号DB
        'host'              => '127.0.0.1', // 默认是连接本地Redis
        'port'              => 6379,        // 默认的端口
        'auth'              => null,        // 默认没有密码
        'connectTimeout'    => 1,           // 连接到服务器的超时时间
        'execTimeout'       => 1,           // 执行操作的超时时间
        'reconnect'         => 3,           // 如果连接断开自动尝试x次重连

        // 链接池配置项
        'intervalCheckTime' => 30 * 1000,   // 池对象回收检测周期
        'maxIdleTime'       => 15,          // 连接最大空闲时间(超时释放)
        'maxObjectNum'      => 20,          // 池最大连接象数量
        'minObjectNum'      => 5,           // 保持的最小连接数量
        'getObjectTimeout'  => 3.0          // 池为空时获取连接最大等待时间

    ])));

    Cache::instance()->addDriver($redisDriver, 'redis');
    $cache = Cache::instance()->getDriver('redis');
});

```

### Memcache驱动

以Swoole的协程Client实现了Memcache的TCP二进制驱动，不依赖PHP本身的Memcached驱动和libmemcache，同样需要协程环境，二进制协议仅支持Memcached(即Server)v1.3版本以上

```php

// 请注意先Use要使用的驱动类和配置类
use EasySwoole\Cache\Config\MemcacheConfig;
use EasySwoole\Cache\Drivers\Memcache as MemcacheDriver;

// 需要协程环境(在EasySwoole框架内无需手动创建协程)
Coroutine::create(function () {

    // Memcache的Config可以同时配置链接池相关的设置项
    $memcacheDriver = new MemcacheDriver((new MemcacheConfig([

        // 链接配置项
        'host'              => '127.0.0.1',  // 默认是连接本地Memcache
        'port'              => 11211,        // 默认的端口

        // 链接池配置项
        'intervalCheckTime' => 30 * 1000,   // 池对象回收检测周期
        'maxIdleTime'       => 15,          // 连接最大空闲时间(超时释放)
        'maxObjectNum'      => 20,          // 池最大连接象数量
        'minObjectNum'      => 5,           // 保持的最小连接数量
        'getObjectTimeout'  => 3.0          // 池为空时获取连接最大等待时间

    ])));

    Cache::instance()->addDriver($memcacheDriver, 'memcache');
    $cache = Cache::instance()->getDriver('memcache');
});

```

### SwooleTable

基于SwooleTable内存表实现的快速内存缓存，注意该缓存暂不支持数据落地，服务停止后缓存数据会立即丢失，适用于做一些计数器类或能容忍缓存重建而不雪崩的一些场景(如储存公众号Token等)，由于Table创建时需要立即申请内存，请确保足够的内存，否则会因Table创建失败导致不可用

> 注意: 由于Swoole进程隔离特性，当使用SwooleTable作为驱动时，需要在Sw的全局期进行驱动的初始化，否则缓存数据会被隔离在各个独立的Worker进程中，另外各配置项受到SwooleTable本身的限制，不能超过本身的限制最大值

```php

// 请注意先Use要使用的驱动类和配置类
use EasySwoole\Cache\Config\SwooleTableConfig;
use EasySwoole\Cache\Drivers\SwooleTable as SwooleTableDriver;

// 需要协程环境(在EasySwoole框架内无需手动创建协程)
Coroutine::create(function () {

    // 可以配置Table相关的一些配置项
    $swooleTableDriver = new SwooleTableDriver((new SwooleTableConfig([

        'tableSize'          => 4096,  // 能容纳的总Key数量(由于哈希冲突，实际储存量会比该值小一点)
        'maxKeySize'         => 512,   // Key支持的最大长度(字节)
        'maxValueSize'       => 4096,  // Value支持的最大长度(字节)
        'recycleInterval'    => 1000,  // 超时Key回收周期(ms)
        'conflictProportion' => 0.2,   // 允许哈希冲突的最大比例

    ])));

    Cache::instance()->addDriver($swooleTableDriver, 'swTable');
    $cache = Cache::instance()->getDriver('swTable');
});

```

### 关于配置项

配置项均由EasySwoole/Spl/SplBean实现，因此可以链式设置和获取

```php

use EasySwoole\Cache\Config\SwooleTableConfig;

$tableConfig = new SwooleTableConfig;

$tableConfig
    ->setTableSize(4096)
    ->setMaxKeySize(512)
    ->setMaxValueSize(512);

```

### 单元测试

尚未支持单元测试，欢迎提交Pull Request!