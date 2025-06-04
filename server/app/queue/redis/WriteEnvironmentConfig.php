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
 * 写入环境变量
 *
 * @author Mr.April
 * @since  1.0
 */
class WriteEnvironmentConfig implements Consumer
{

    // 要消费的队列名
    public string $queue = 'write-environment-config';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public string $connection = 'default';

    public function consume($data)
    {
        $filePath = base_path() . '/.env';
        $this->WriteEnvironmentConfig($filePath, $data);
    }

    /**
     * 写入环境配置
     *
     * @param string $file
     * @param array  $params
     */
    protected function writeEnvironmentConfig(string $file, array $params)
    {
        $config_content = <<<EOF
                                # Application Environment
                                APP_ENV=local
                                APP_DEBUG=false
                                
                                # Database Configuration
                                DB_CONNECTION=mysql
                                DB_HOST={$params['host']}
                                DB_PORT={$params['port']}
                                DB_DATABASE={$params['database']}
                                DB_USERNAME={$params['username']}
                                DB_PASSWORD={$params['password']}
                                DB_PREFIX=ma_
                                
                                # Redis Configuration
                                REDIS_HOST=127.0.0.1
                                REDIS_PORT=6379
                                REDIS_PASSWORD=null
                                REDIS_DB=0
                                
                                # Queue Redis Configuration
                                QUEUE_REDIS_HOST=127.0.0.1
                                QUEUE_REDIS_PORT=6379
                                QUEUE_REDIS_PASSWORD=null
                                QUEUE_REDIS_DB=0
                                QUEUE_REDIS_PREFIX=queue
                                
                                # Cache Configuration
                                CACHE_CUSTOM_REDIS_HOST=127.0.0.1
                                CACHE_CUSTOM_REDIS_PORT=6379
                                CACHE_CUSTOM_REDIS_PASSWORD=null
                                CACHE_CUSTOM_REDIS_DB=0
                                CACHE_CUSTOM_REDIS_PREFIX=cache_custom
                                
                                # Feature Toggles
                                TASK_ENABLED=false                 # Timer switch
                                TENANT_ENABLED=true                # Tenant mode switch
                                CAPTCHA_ENABLED=true               # Captcha switch
                                CAPTCHA_MODE=session               # Captcha mode redis|session
                                RECYCLE_BIN_ENABLED=true           # Recycle bin mode switch
                                
                                
                                
                                # Other Configuration

                                EOF;
        file_put_contents($file, $config_content);
    }

}
