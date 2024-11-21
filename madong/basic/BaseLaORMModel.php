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

namespace madong\basic;

use madong\utils\Snowflake;
use support\Model;

class BaseLaORMModel extends Model
{
    private const WORKER_ID = 1;
    private const DATA_CENTER_ID = 1;

    /**
     * 指明模型的ID是否自动递增。
     *
     * @var bool
     */
    public $incrementing = false;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'updated_time';

    /**
     * 指示模型是否主动维护时间戳。
     *
     * @var bool
     */
    public $timestamps = true;

    // 存储动态隐藏字段
    protected array $dynamicHidden = [];

    protected static function boot()
    {
        parent::boot();
        //注册创建事件
        static::creating(function ($model) {
            if (!isset($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::generateSnowflakeID(); // 生成雪花 ID
            }
            self::setCreatedBy($model);
        });

        // 注册更新事件
        static::updating(function ($model) {
            self::setUpdatedBy($model);
        });
    }

    /**
     * 兼容tp写法
     *
     * @param string $field
     *
     * @return mixed
     */
    public function getData(string $field): mixed
    {
        return $this->attributes[$field] ?? null;
    }

    /**
     * 兼容 TP
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * 兼容tp 重写动态输出隐藏
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields): static
    {
        $this->dynamicHidden = array_merge($this->dynamicHidden, $fields);
        return $this; // 支持链式调用
    }

    /**
     * 设置创建人
     *
     * @param Model $model
     *
     * @return void
     */
    private static function setCreatedBy(Model $model): void
    {
        $uid = getCurrentUser();
        if ($uid && $model->isFillable('created_by')) {
            $model->setAttribute('created_by', $uid);
        }
    }

    /**
     * 设置更新人
     *
     * @param Model $model
     *
     * @return void
     */
    private static function setUpdatedBy(Model $model): void
    {
        $uid = getCurrentUser();
        if ($uid && $model->isFillable('updated_by')) {
            $model->setAttribute('updated_by', $uid);
        }
    }

    /**
     * 生成雪花ID
     *
     * @return int
     */
    private static function generateSnowflakeID(): int
    {
        $snowflake = new Snowflake(self::WORKER_ID, self::DATA_CENTER_ID);
        return $snowflake->nextId();
    }

}