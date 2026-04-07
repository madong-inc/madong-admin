<?php
declare(strict_types=1);

namespace app\enum\web;

use core\interface\IEnum;

/**
 * 目标窗口枚举
 */
enum MenuTarget: int implements IEnum
{
    case SELF = 1;  // 当前窗口
    case BLANK = 2; // 新窗口

    /**
     * 获取文本
     */
    public function text(): string
    {
        return match ($this) {
            self::SELF => '当前窗口',
            self::BLANK => '新窗口',
        };
    }

    /**
     * 获取标签
     */
    public function label(): string
    {
        return $this->text();
    }
}
