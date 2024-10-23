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

use madong\exception\ApiException;
use madong\trait\ModelTrait;
use madong\utils\Snowflake;
use think\Model;
use think\model\concern\SoftDelete;

class BaseSoftDeleteModel extends Model
{
    use ModelTrait;
    use SoftDelete;

    protected $deleteTime = 'delete_time';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $hidden = ['delete_time'];
    protected $readonly = ['created_by', 'create_time'];

    private const WORKER_ID = 1;
    private const DATA_CENTER_ID = 1;

    /**
     * 获取模型定义的字段列表
     *
     * @return mixed
     */
    public function getFields(): mixed
    {
        return $this->getTableFields();
    }

    /**
     * 获取模型定义的数据库表名【全称】
     *
     * @return string
     */
    public static function getTableName(): string
    {
        $self = new static();
        return $self->getConfig('prefix') . $self->name;
    }

    /**
     * 新增事件
     *
     * @param Model $model
     *
     * @return void
     * @throws ApiException
     */
    public static function onBeforeInsert(Model $model): void
    {
        try {
            self::setCreatedBy($model);
            self::setPrimaryKey($model);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * 写入事件
     *
     * @param Model $model
     *
     * @return void
     */
    public static function onBeforeWrite(Model $model): void
    {
        self::setUpdatedBy($model);
    }

    /**
     * 设置创建人
     *
     * @param Model $model
     * @return void
     */
    private static function setCreatedBy(Model $model): void
    {
        $uid = getCurrentUser();
        if ($uid) {
            $model->setAttr('created_by', $uid);
        }
    }

    /**
     * 设置更新人
     *
     * @param Model $model
     * @return void
     */
    private static function setUpdatedBy(Model $model): void
    {
        $uid = getCurrentUser();
        if ($uid) {
            $model->setAttr('updated_by', $uid);
        }
    }

    /**
     * 设置主键
     *
     * @param Model $model
     * @return void
     */
    private static function setPrimaryKey(Model $model): void
    {
        $flakeId = $model->{$model->pk} ?? self::generateSnowflakeID();
        $model->{$model->pk} = $flakeId;
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
