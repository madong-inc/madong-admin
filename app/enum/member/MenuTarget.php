<?php
declare(strict_types=1);

namespace app\enum\member;



use core\interface\IEnum;

/**
 * 菜单目标窗口枚举
 */
enum MenuTarget: int implements IEnum
{

    /**
     * 当前窗口
     */
    case SELF = 1;

    /**
     * 新窗口
     */
    case BLANK = 2;

    /**
     * 获取枚举文本
     */
    public function label(): string
    {
        return match ($this) {
            self::SELF => '当前窗口',
            self::BLANK => '新窗口',
        };
    }
}