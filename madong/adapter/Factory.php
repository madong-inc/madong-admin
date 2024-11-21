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

namespace madong\adapter;

use madong\exception\MadongException;
use support\Container;
use think\Model as thinkModel;
use support\Model as laravelModel;

class Factory
{

    /**
     * 模型
     *
     * @param string                                  $mode
     * @param string|\think\Model|\support\Model|null $model
     *
     * @return mixed
     */
    public static function create(string $mode, string|thinkModel|laravelModel|null $model = null): mixed
    {
        return match ($mode) {
            'thinkORM' => Container::make(ThinkRepository::class, ['context' => $model]),
            'laravelORM' => Container::make(LaravelRepository::class, ['context' => $model]),
            default => throw new MadongException("Invalid type"),
        };
    }
}
