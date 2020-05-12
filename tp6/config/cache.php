<?php
use think\facade\Env;

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => Env::get('cache.driver', 'redis'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的缓存连接
        'redis'   => [
            'type'   => 'redis' ,
            'host'   => Env::get('redis.hostname', '172.28.3.157') ,
            'password' => Env::get('redis.password', '') ,
            'select' => 2,
            // 全局缓存有效期（0为永久有效）
            'expire' => 60 ,
            'port'=> 6379,
            // 缓存前缀
            'prefix' => 'tkmall:api:' ,
        ] ,
    ],
];
