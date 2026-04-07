<?php
return [
    'enable'     => true,
    // 模块命名限制
    'module_name_restrictions' => [
        'enabled' => true,
        'reserved_names' => [
            'system',
            'role',
            'admin',
            'auth',
            'user',
            'permission',
            'menu',
            'dept',
            'post',
            'dict',
            'config',
            'log',
            'monitor',
            'plugin',
            'dev',
            'test',
            'generator'
        ],
        'pattern' => '/^[a-z0-9_-]+$/'
    ],
];