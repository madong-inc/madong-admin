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

namespace core\enum\system;

enum MessagePriority: int
{
    case EMERGENCY = 1; // 紧急 - 最高优先级
    case URGENT = 2;    // 急迫
    case NORMAL = 3;    // 普通 - 默认优先级

    //定义一个方法以获取状态的描述
    public function label(): string
    {
        return match ($this) {
            self::EMERGENCY => '紧急',
            self::URGENT => '急迫',
            self::NORMAL => '普通'
        };
    }

    //定义一个方法设置标签颜色-如果没有定义自动生成
    public function color(): string
    {
        return match ($this) {
            self::EMERGENCY => 'red',
            self::URGENT => 'orange',
            self::NORMAL => 'blue'
        };
    }

}
