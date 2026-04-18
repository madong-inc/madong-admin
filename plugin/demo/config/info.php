<?php

/**
 * 插件信息配置
 */

return [
    'name'         => 'demo',
    'identifier'   => 'demo',
    'version'      => '1.0.0',
    'description'  => '示例插件',
    'author'       => 'Madong',
    'author_email' => 'support@madong.tech',
    'website'      => 'https://www.madong.tech',
    'require'      => [
        'composer' => [

        ],
    ],
    'npm_require'  => [
        'admin' => [

        ],
        'web'   => [

        ],
    ],
    'uninstall'    => [
        'undeletable'         => false,
        'drop_tables'         => false,
        'remove_dependencies' => false,
    ],
    'type'         => 'madong',
];
