<?php

return [
    'templates' => [
        // 本地存储配置
        [
            'group_code' => 'storage',
            'code' => 'local',
            'name' => '本地存储配置',
            'content' => [
                'root' => 'public',
                'dirname' => 'upload',
                'domain' => 'http://127.0.0.1:8001'
            ],
            'is_sys' => 1
        ],
        // OSS存储配置
        [
            'group_code' => 'storage',
            'code' => 'oss',
            'name' => 'OSS存储配置',
            'content' => [
                'accessKeyId' => '',
                'accessKeySecret' => '',
                'bucket' => '',
                'domain' => '',
                'endpoint' => '',
                'dirname' => ''
            ],
            'is_sys' => 1
        ],
        // COS存储配置
        [
            'group_code' => 'storage',
            'code' => 'cos',
            'name' => 'COS存储配置',
            'content' => [
                'secretId' => '',
                'secretKey' => '',
                'bucket' => '',
                'domain' => '',
                'region' => '',
                'dirname' => ''
            ],
            'is_sys' => 1
        ],
        // 七牛云存储配置
        [
            'group_code' => 'storage',
            'code' => 'qiniu',
            'name' => '七牛云存储配置',
            'content' => [
                'accessKey' => '',
                'secretKey' => '',
                'bucket' => '',
                'domain' => '',
                'region' => '',
                'dirname' => ''
            ],
            'is_sys' => 1
        ],
        // S3存储配置
        [
            'group_code' => 'storage',
            'code' => 's3',
            'name' => 'S3存储配置',
            'content' => [
                'key' => '',
                'secret' => '',
                'bucket' => '',
                'dirname' => '',
                'domain' => '',
                'region' => '',
                'version' => '',
                'endpoint' => '',
                'acl' => ''
            ],
            'is_sys' => 1
        ],
    ],
];