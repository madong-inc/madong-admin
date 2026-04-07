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
 * Official Website: http://www.madong.tech
 */

namespace app\enum\review;

use core\interface\IEnum;

/**
 * 审核状态枚举
 */
enum ReviewStatus: int implements IEnum
{
    case PENDING = 0;   // 待审核
    case APPROVED = 1;  // 已通过
    case REJECTED = 2;  // 已拒绝
    case CANCELED = 3;  // 已取消

    /**
     * 获取人类可读的标签
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => '待审核',
            self::APPROVED => '已通过',
            self::REJECTED => '已拒绝',
            self::CANCELED => '已取消',
        };
    }

    /**
     * 获取对应的颜色值
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => '#FF9800',  // 橙色
            self::APPROVED => '#4CAF50', // 绿色
            self::REJECTED => '#FF5252', // 红色
            self::CANCELED => '#9E9E9E', // 灰色
        };
    }

    /**
     * 根据状态值获取标签
     */
    public static function getLabel(int $value): string
    {
        return self::tryFrom($value)?->label() ?? '未知';
    }

    /**
     * 根据状态值获取颜色
     */
    public static function getColor(int $value): string
    {
        return self::tryFrom($value)?->color() ?? '#9E9E9E';
    }

    /**
     * 获取所有状态选项（用于下拉框等）
     */
    public static function options(): array
    {
        return array_map(
            fn(self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
            ],
            self::cases()
        );
    }
}
