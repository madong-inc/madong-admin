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

namespace madong\adapters;

use madong\exception\MadongException;
use madong\factories\LaravelORMFactory;
use madong\factories\ThinkORMFactory;
use support\Container;
use support\Model as laravelModel;
use think\Model as thinkModel;

class ORMAdapterFactory
{

    public static function createAdapter(string $mode, string|thinkModel|laravelModel|null $model = null): mixed
    {
        return match ($mode) {
            'thinkORM' => Container::make(ThinkORMFactory::class, ['context' => $model]),
            'laravelORM' => Container::make(LaravelORMFactory::class, ['context' => $model]),
            default => throw new MadongException("Invalid type"),
        };
    }
}
