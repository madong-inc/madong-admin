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

enum Color: string
{
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';

    public function getDescription(): string
    {
        return match ($this) {
            self::RED => '红色',
            self::GREEN => '绿色',
            self::BLUE => '蓝色',
        };
    }

    public static function getName(): string
    {
        return '颜色枚举';
    }
}
