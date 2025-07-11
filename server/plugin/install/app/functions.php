<?php

/**
 * 设置是否允许下一步
 *
 * @param $val
 */
function setOk($val): void
{
    global $isOK;
    $isOK = $val;
}

/**
 * 测试可写性
 *
 * @param $file
 */
function isWrite($file): void
{
    if (is_writable(base_path() . $file)) {
        echo '<b class="green">可写</b>';
    } else {
        echo '<span>不可写</span>';
        setOk(false);
    }
}

/**
 * 测试函数是否存在
 *
 * @param $func
 *
 * @return bool|mixed
 */
function isFunExists($func): mixed
{
    $state = function_exists($func);
    if ($state === false) {
        setOk(false);
    }
    return $state;
}

/**
 * 测试函数是否存在
 *
 * @param $func
 */
function isFunExistsTxt($func): void
{
    if (isFunExists($func)) {
        echo '<b class="layui-icon green">&#xe697;</b>';
    } else {
        echo '<span>需安装</span>';
        setOk(false);
    }
}

/**
 * 获取PHP扩展依赖列表
 *
 * @return array 包含扩展名称和加载状态的数组
 */
function getRequiredExtensions(): array
{
    $extensions = [
        [
            'name'   => 'CURL',
            'status' => extension_loaded('curl'),
        ],
        [
            'name'   => 'OpenSSL',
            'status' => extension_loaded('openssl'),
        ],
        [
            'name'   => 'PDO Mysql',
            'status' => extension_loaded('PDO') && extension_loaded('pdo_mysql'),
        ],
        [
            'name'   => 'Mysqlnd',
            'status' => extension_loaded('mysqlnd'),
        ],
        [
            'name'   => 'JSON',
            'status' => extension_loaded('json'),
        ],
        [
            'name'   => 'Fileinfo',
            'status' => extension_loaded('fileinfo'),
        ],
        [
            'name'   => 'GD',
            'status' => extension_loaded('gd'),
        ],
        [
            'name'   => 'BCMath',
            'status' => extension_loaded('bcmath'),
        ],
        [
            'name'   => 'Mbstring',
            'status' => extension_loaded('mbstring'),
        ],
        [
            'name'   => 'SimpleXML',
            'status' => extension_loaded('SimpleXML'),
        ],
    ];

    foreach ($extensions as $ext) {
        if (!$ext['status']) {
            setNextStepAllowed(false);
        }
    }
    return $extensions;
}

/**
 * 生成环境配置模板
 *
 * @return string
 */
function generateEnvTemplate(): string
{
    return <<<EOT
                # Application Environment
                APP_ENV=local
                APP_DEBUG=false
                APP_TERMINAL_ENABLED=false
                
                # Database Configuration
                DB_CONNECTION=mysql
                DB_HOST    = ~db_host~
                DB_PORT    = ~db_port~
                DB_DATABASE= ~db_name~
                DB_USERNAME= ~db_user~
                DB_PASSWORD= ~db_pwd~
                DB_PREFIX  = ~db_prefix~
                
                # Redis Configuration
                REDIS_HOST= ~redis_host~
                REDIS_PORT= ~redis_port~
                REDIS_PASSWORD= ~redis_pwd~
                REDIS_DB=0
                
                # Queue Redis Configuration
                QUEUE_REDIS_HOST= ~redis_host~
                QUEUE_REDIS_PORT= ~redis_port~
                QUEUE_REDIS_PASSWORD= ~redis_pwd~
                QUEUE_REDIS_DB=0
                QUEUE_REDIS_PREFIX=queue
                
                # Cache Configuration
                CACHE_CUSTOM_REDIS_HOST= ~redis_host~
                CACHE_CUSTOM_REDIS_PORT= ~redis_port~
                CACHE_CUSTOM_REDIS_PASSWORD= ~redis_pwd~
                CACHE_CUSTOM_REDIS_DB=0
                CACHE_CUSTOM_REDIS_PREFIX=cache_custom
                
                # Feature Toggles
                APP_TASK_ENABLED=false                 # Timer switch
                APP_TENANT_ENABLED=true            # Tenant mode switch
                APP_TENANT_AUTO_SELECT_FIRST=false # Tenant auto
                CAPTCHA_ENABLED=false              # Captcha switch
                CAPTCHA_MODE=session               # Captcha mode redis|session
                RECYCLE_BIN_ENABLED=true           # Recycle bin mode switch
                
                
                # Other Configuration
                EOT;
}
