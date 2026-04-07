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

namespace resource\database\seeds;

use app\model\system\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    private ?array $adminParams = null;

    /**
     * 设置管理员参数
     */
    public function setAdminParams(array $params): void
    {
        $this->adminParams = $params;
    }

    public function run(): void
    {
        // 清空表
        Admin::truncate();

        // 检查是否已存在管理员
        $existingAdmin = Admin::find(1);
        if ($existingAdmin) {
            // 更新现有管理员
            if ($this->adminParams) {
                $existingAdmin->user_name = $this->adminParams['username'];
                $existingAdmin->password = password_hash($this->adminParams['password'], PASSWORD_DEFAULT);
                $existingAdmin->real_name = '超级管理员';
                $existingAdmin->nick_name = '超级管理员';
                $existingAdmin->is_super = 1;
                $existingAdmin->enabled = 1;
                $existingAdmin->email = $this->adminParams['email'] ?? 'admin@example.com';
                $existingAdmin->save();
            }
        } else {
            // 创建新管理员
            $username = $this->adminParams['username'] ?? 'admin';
            $password = $this->adminParams['password'] ?? '123456';

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            Admin::create([
                'id'         => 1,
                'user_name'  => $username,
                'real_name'  => '超级管理员',
                'nick_name'  => '超级管理员',
                'password'   => $hashedPassword,
                'email'      => 'admin@example.com',
                'avatar'     => '',
                'is_super'   => 1,
                'enabled'    => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }
}
