<?php
declare(strict_types=1);

namespace app\enum\member;



use core\interface\IEnum;

/**
 * 菜单类型枚举
 */
enum MenuType: int implements IEnum
{

    /**
     * 普通菜单
     */
    case NORMAL = 1;

    /**
     * 开通菜单
     */
    case OPEN = 2;

    /**
     * 获取枚举文本
     */
    public function label(): string
    {
        return match ($this) {
            self::NORMAL => '普通菜单',
            self::OPEN => '开通菜单',
        };
    }
}