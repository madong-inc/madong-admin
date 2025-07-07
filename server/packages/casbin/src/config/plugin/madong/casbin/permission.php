<?php

return [
    'default' => 'basic',
    /** 日志配置 */
    'log'     => [
        'enabled' => false, // changes will log messages to the Logger.
        'logger'  => 'Casbin', // Casbin Logger, Supported: \Psr\Log\LoggerInterface|string
        'path'    => runtime_path() . '/logs/casbin.log', // log path
    ],
    /** 默认配置 */
    'basic'   => [
        // 策略模型Model设置
        'model'    => [
            'config_type'      => 'file',
            'config_file_path' => config_path() . '/plugin/madong/casbin/rbac-model.conf',
            'config_text'      => '',
        ],
        // 适配器
        'adapter'  => madong\casbin\adapter\DatabaseAdapter::class,
        // 数据库设置
        'database' => [
            'connection'  => '',
            'rules_table' => 'casbin_rule',
            'rules_name'  => null,
        ],
    ],
    /** 其他扩展配置，只需要按照基础配置一样，复制一份，指定相关策略模型和适配器即可 */
    'restful' => [
        'model'    => [
            'config_type'      => 'file',
            'config_file_path' => config_path() . '/plugin/madong/casbin/restful-model.conf',
            'config_text'      => '',
        ],
        'adapter'  => madong\casbin\adapter\DatabaseAdapter::class,//适配器
        'database' => [
            'connection'  => '',
            'rules_table' => 'restful_casbin_rule',
            'rules_name'  => null,
        ],
    ],
];
