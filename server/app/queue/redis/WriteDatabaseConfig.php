<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitcode.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

/**
 * 删除导出的残留excel文件
 *
 * @author Mr.April
 * @since  1.0
 */
class WriteDatabaseConfig implements Consumer
{

    // 要消费的队列名
    public string $queue = 'write-database-config';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public string $connection = 'default';

    public function consume($data)
    {
        $filePath = base_path() . '/config/database.php';
        $this->writeDatabaseConfig($filePath, $data);
    }

    protected function writeDatabaseConfig(string $file, array $params)
    {
        $config_content = <<<EOF
                                <?php
                                    return  [
                                        'default' => 'mysql',
                                        'connections' => [
                                            'mysql' => [
                                                'driver'      => 'mysql',
                                                'host'        => '{$params['host']}',
                                                'port'        => '{$params['port']}',
                                                'database'    => '{$params['database']}',
                                                'username'    => '{$params['username']}',
                                                'password'    => '{$params['password']}',
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
                                EOF;
        file_put_contents($file, $config_content);
    }

}
