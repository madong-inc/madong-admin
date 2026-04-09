<?php

/**
 * 插件信息配置
 */

return [
        'name' => 'example',
        'identifier' => 'example',
        'version' => '1.0.0',
        'description' => 'Swagger示例插件',
        'author' => 'Madong',
        'author_email' => 'support@madong.tech',
        'website' => 'https://www.madong.tech',
        'require' => [
            'composer' => [
                'ramsey/test' => '^4.7',
            ],
        ],
        'npm_require' => [
            'admin' => [
                'lodash' => '^4.17.21',
            ],
            'web' => [
                'axios' => '^1.6.0',
            ],
        ],
        'uninstall' => [
            'undeletable' => true,
            'drop_tables' => false,
            'remove_dependencies' => false,
        ],
        'type' => 'madong',
    ];
