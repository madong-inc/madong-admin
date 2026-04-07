<?php

/**
 * 插件信息配置
 */

return [
    // 基础信息
    'name'         => 'example',
    'identifier'   => 'example',
    'version'      => '1.0.0',
    'description'  => 'Swagger示例插件',
    'author'       => 'Madong',
    'author_email' => 'support@madong.tech',
    'website'      => 'https://www.madong.tech',

    // Composer 依赖测试
    'require'      => [
        'composer' => [
            'ramsey/test' => '^4.7',
        ],
    ],

    // NPM 依赖测试
    'npm_require'  => [
        'admin' => [
            'lodash' => '^4.17.21',
        ],
        'web'   => [
            'axios' => '^1.6.0',
        ],
    ],

    // 卸载配置
    'uninstall'    => [
        'undeletable'         => true,  // 是否不可删除（true: 系统内置插件不可删除, false: 可删除）
        'drop_tables'         => false,  // 删除数据表
        'remove_dependencies' => false,  // 移除合并的依赖
    ],
];
