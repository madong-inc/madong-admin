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

/**
 * 数据回收站-只能通过模型删除并且添加表配置的的才有效【直接使用DB操作需手动处理回收数据】
 */
return [
    // 全局默认配置
    'default' => [
        'enabled'      => true,       // 是否启用回收站
        'strategy'     => 'logical',  // logical|physical 默认软删
        'storage_days' => 30,         // 保留天数
        'storage_mode' => 'central',  // central|isolated (主库集中|分库隔离)
    ],

    // 表级配置覆盖
    'tables'  => [
        'sys_upload' => [
            'strategy'     => 'physical',
            'storage_mode' => 'isolated',//租户数据源
        ],
        'sys_menu'   => [
            'strategy'     => 'physical',
            'storage_mode' => 'central',//主库数据源
        ],
    ],
];

