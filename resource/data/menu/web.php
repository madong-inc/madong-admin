<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.tech
 */

return [
    [
        'app'      => 'web',
        'category' => 1,
        'source'   => 'system',
        'code'     => null,
        'name'     => '首页',
        'url'      => '/',
        'icon'     => 'ant-design:home-outlined',
        'level'    => 1,
        'type'     => 2,
        'sort'     => -1,
        'target'   => 1,
        'is_show'  => 1,
        'enabled'  => 1,
        'created_at' => time(),
        'updated_at' => time(),
        'deleted_at' => null
    ],
    [
        'app'      => 'web',
        'category' => 2,
        'source'   => 'system',
        'code'     => 'test3',
        'name'     => '我的账户',
        'url'      => null,
        'icon'     => 'ant-design:idcard-outlined',
        'level'    => 1,
        'type'     => 1,
        'sort'     => 999,
        'target'   => 1,
        'is_show'  => 1,
        'enabled'  => 1,
        'created_at' => time(),
        'updated_at' => time(),
        'deleted_at' => null,
        'children' => [
            [
                'app'      => 'web',
                'category' => 2,
                'source'   => 'system',
                'code'     => '',
                'name'     => '修改密码',
                'url'      => '/member/password',
                'icon'     => 'ant-design:lock-outlined',
                'level'    => 1,
                'type'     => 2,
                'sort'     => 999,
                'target'   => 1,
                'is_show'  => 1,
                'enabled'  => 1,
                'created_at' => time(),
                'updated_at' => time(),
                'deleted_at' => null
            ],
            [
                'app'      => 'web',
                'category' => 2,
                'source'   => 'system',
                'code'     => '',
                'name'     => '用户设置',
                'url'      => '/member/settings',
                'icon'     => 'ant-design:setting-outlined',
                'level'    => 1,
                'type'     => 2,
                'sort'     => 10,
                'target'   => 1,
                'is_show'  => 1,
                'enabled'  => 1,
                'created_at' => time(),
                'updated_at' => time(),
                'deleted_at' => null
            ],
            [
                'app'      => 'web',
                'category' => 2,
                'source'   => 'system',
                'code'     => '',
                'name'     => '个人资料',
                'url'      => '/member/profile',
                'icon'     => 'ant-design:user-outlined',
                'level'    => 1,
                'type'     => 2,
                'sort'     => -1,
                'target'   => 1,
                'is_show'  => 1,
                'enabled'  => 1,
                'created_at' => time(),
                'updated_at' => time(),
                'deleted_at' => null
            ],
            [
                'app'      => 'web',
                'category' => 2,
                'source'   => 'system',
                'code'     => 'test1',
                'name'     => '积分记录',
                'url'      => '/member/points',
                'icon'     => 'ant-design:gift-outlined',
                'level'    => 1,
                'type'     => 2,
                'sort'     => 999,
                'target'   => 1,
                'is_show'  => 1,
                'enabled'  => 1,
                'created_at' => time(),
                'updated_at' => time(),
                'deleted_at' => null
            ],
            [
                'app'      => 'web',
                'category' => 2,
                'source'   => 'system',
                'code'     => 'test2',
                'name'     => '每日签到',
                'url'      => '/member/sign',
                'icon'     => 'ant-design:calendar-outlined',
                'level'    => 1,
                'type'     => 2,
                'sort'     => 999,
                'target'   => 1,
                'is_show'  => 1,
                'enabled'  => 1,
                'created_at' => time(),
                'updated_at' => time(),
                'deleted_at' => null
            ]
        ]
    ]
];


