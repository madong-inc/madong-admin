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

namespace app\enum;

enum Status: string
{
    case PENDING = 'pending';   // 待处理
    case APPROVED = 'approved';  // 已批准
    case REJECTED = 'rejected';  // 已拒绝
    case COMPLETED = 'completed'; // 已完成

    // 定义一个方法以获取状态的描述
    public function getDescription(): string
    {
        return match ($this) {
            self::PENDING => '待处理',
            self::APPROVED => '已批准',
            self::REJECTED => '已拒绝',
            self::COMPLETED => '已完成',
        };
    }

    // 静态方法返回枚举名称
    public static function getName(): string
    {
        return '状态';
    }
}
