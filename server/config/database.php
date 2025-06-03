<?php
    return  [
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver'      => 'mysql',
                'host'        => '127.0.0.1',
                'port'        => '3306',
                'database'    => 'madong_community',
                'username'    => 'root',
                'password'    => 'root',
                'unix_socket' => '',
                'charset'     => 'utf8',
                'collation'   => 'utf8_unicode_ci',
                'prefix'      => 'ma_',
                'strict'      => true,
                'engine'      => null,
                'pool' => [ 
                   // 连接池配置，仅支持swoole/swow驱动
                   'max_connections' => 5, // 最大连接数
                   'min_connections' => 1, // 最小连接数
                   'wait_timeout' => 3,    // 从连接池获取连接等待的最大时间，超时后会抛出异常
                   'idle_timeout' => 60,   // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
                   'heartbeat_interval' => 50, // 连接池心跳检测时间，单位秒，建议小于60秒
                ],
            ],
        ],
    ];