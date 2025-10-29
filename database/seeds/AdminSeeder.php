<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

declare(strict_types=1);

namespace database\seeds;

use app\common\model\system\SysAdmin;
use app\common\model\system\SysRole;
use Illuminate\Database\Seeder;
use support\Db;
use core\uuid\Snowflake;

class AdminSeeder extends Seeder
{
    public function run(): void
    {

        SysRole::truncate();
        SysAdmin::truncate();

        $hashedPassword = password_hash('123456', PASSWORD_DEFAULT); // 密码：123456
        $entity         = SysAdmin::create([
            'id'         => 1,                      // 超级管理员id=1
            'user_name'  => 'admin',                // 账号
            'real_name'  => '超级管理员',            // 用户
            'nick_name'  => '超级管理员',            // 昵称
            'password'   => $hashedPassword,        // 加密后的密码
            'email'      => 'admin@example.com',    // 邮箱
            'avatar'     => '',                     // 头像
            'is_super'   => 1,                      //是否超级管理员
            'enabled'    => 1,                      //启用状态
            'created_at' => time(),                 // 创建时间
            'updated_at' => time(),                 // 更新时间
        ]);

        $role = SysRole::create([
            'id'             => 1,
            'name'           => '超级管理员',
            'code'           => 'SuperAdmin',
            'is_super_admin' => 1,
        ]);
        $entity->roles()->sync($role);
    }
}
