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
        'id'              => 1,//超级管理员id
        'user_name'       => 'admin',
        'real_name'       => '超级管理员',
        'nick_name'       => '超级管理员',
        'password'        => '$2y$10$X1CoPxnqOZPIuyOk/RSXoOIVxflBZVUyqF/8fYOKzvn2hk0VGU52C',
        'is_super'        => 1,//是否超级管理员
        'mobile_phone'    => 18888888888,
        'email'           => '',
        'avatar'          => '/upload/default.png',
        'signed'          => 'Today is very good！',
        'dashboard'       => '',
        'dept_id'         => '',
        'enabled'         => 1,
        'login_ip'        => '',
        'login_time'      => '',
        'backend_setting' => json_encode((object)[]),
        'created_by'      => 1,
        'updated_by'      => 1,
        'created_at'      => time(),
        'updated_at'      => time(),
        'deleted_at'      => null,
        'sex'             => 0,
        'remark'          => '',
        'birthday'        => '',
        'tel'             => '',
        'is_locked'       => 0,
    ],
];