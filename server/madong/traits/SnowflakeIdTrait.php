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

namespace madong\traits;

use madong\exception\ApiException;
use madong\utils\Snowflake;

trait SnowflakeIdTrait
{

    /**
     * 新增自动创建id
     *
     * @param $model
     *
     * @return void
     * @throws \ingenstream\exception\ApiException
     */
    protected static function onBeforeInsert($model): void
    {
        try {
            $flakeId             = !empty($model->{$model->pk}) ? $model->{$model->pk} : self::generateSnowflakeID();
            $model->{$model->pk} = $flakeId;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * 生成雪花ID
     *
     * @return int
     */
    private static function generateSnowflakeID(): int
    {
        $workerId     = 1;
        $dataCenterId = 1;
        $snowflake    = new Snowflake($workerId, $dataCenterId);
        return $snowflake->nextId();
    }
}
