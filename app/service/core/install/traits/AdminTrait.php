<?php
declare(strict_types=1);
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

namespace app\service\core\install\traits;

use core\uuid\Snowflake;

/**
 * 管理员创建 Trait
 */
trait AdminTrait
{

    /**
     * 创建管理员
     */
    public function createAdmin(array $adminParams): void
    {
        $adminTable = $this->table('sys_admin');
        $pdo = $this->getPdo();
        
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM `{$adminTable}`");
        $result = $stmt->fetch();
        if ($result['cnt'] > 0) {
            return;
        }

        $username = $adminParams['username'] ?? 'admin';
        $password = $adminParams['password'] ?? '123456';
        $email = $adminParams['email'] ?? 'admin@example.com';

        $this->insert($adminTable, [
            'id' => 1,
            'user_name' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'real_name' => '超级管理员',
            'nick_name' => '超级管理员',
            'email' => $email,
            'is_super' => 1,
            'enabled' => 1,
            'created_at' => $this->currentTime,
            'updated_at' => $this->currentTime,
        ]);
    }
}
