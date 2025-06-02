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

use app\common\model\system\SystemTenant;
use app\common\scopes\global\TenantScope;
use app\common\services\system\SystemRecycleBinService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use madong\exception\AdminException;
use madong\helper\Snowflake;
use support\Container;
use support\Model;

class BaseModel extends Model
{
    private const WORKER_ID = 1;
    private const DATA_CENTER_ID = 1;

    // 定义常量
    protected const CONNECTION_NAME = 'mysql';

    /**
     * 指明模型的ID是否自动递增。
     *
     * @var bool
     */
    public $incrementing = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const DELETED_AT = 'deleted_at';

    protected $appends = [];

    /**
     * 隐藏属性
     *
     * @var array
     */
    protected $hidden = ['tenant_id'];

    /**
     * 模型日期字段的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 指示模型是否主动维护时间戳。
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 雪花算法实例
     *
     * @var Snowflake|null
     */
    protected static ?Snowflake $snowflake = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }

    protected static function boot()
    {
        parent::boot();
        //注册创建事件
        static::creating(function ($model) {
            if (!isset($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::generateSnowflakeID(); // 生成雪花 ID
            }
            self::setCreatedBy($model);
            self::setCreatedDept($model);
            self::setTenantId($model);
        });

        // 注册更新事件
        static::updating(function ($model) {
            self::setUpdatedBy($model);
        });

        // 注册删除事件
        static::deleted(function ($model) {
            self::onAfterDelete($model);
        });
    }

    /**
     * 是否开启软删
     *
     * @return bool
     */
    public static function isSoftDeleteEnabled(): bool
    {
        return in_array(SoftDeletes::class, class_uses(static::class));
    }

    /**
     * 获取主键名称
     *
     * @return string
     */
    public function getPk(): string
    {
        return $this->getKeyName();
    }

    /**
     * 获取模型字段数据
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
     * 写入模型字段数据
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * 获取模型的字段列表
     *
     * @return array
     */
    public function getFields(): array
    {
        try {
            $tableName     = $this->getTable();
            $connection    = $this->getConnection();
            $prefix        = $connection->getTablePrefix();
            $fullTableName = $prefix . $tableName;
            $fields        = $connection->select("SHOW COLUMNS FROM `{$fullTableName}`");
            return array_map(function ($column) {
                return $column->Field;
            }, $fields);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 追加创建时间
     *
     * @return string|null
     */
    public function getCreatedDateAttribute(): ?string
    {
        if ($this->getAttribute($this->getCreatedAtColumn())) {
            try {
                $timestamp = $this->getRawOriginal($this->getCreatedAtColumn());
                if (empty($timestamp)) {
                    return null;
                }
                $carbonInstance = Carbon::createFromTimestamp($timestamp);
                return $carbonInstance->setTimezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 追加更新时间
     *
     * @return string|null
     */
    public function getUpdatedDateAttribute(): ?string
    {
        if ($this->getAttribute($this->getUpdatedAtColumn())) {
            try {
                $timestamp = $this->getRawOriginal($this->getUpdatedAtColumn());
                if (empty($timestamp)) {
                    return null;
                }
                $carbonInstance = Carbon::createFromTimestamp($timestamp);
                return $carbonInstance->setTimezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 删除事件
     *
     * @param \support\Model $model
     */
    public static function onAfterDelete(Model $model)
    {
        try {
            if ($model->isSoftDeleteEnabled()) {
                return;
            }
            $table     = $model->getTable();
            $tableData = $model->getAttributes();
            $prefix    = $model->getConnection()->getTablePrefix();
            if (self::shouldStoreInRecycleBin($table)) {
                $data                    = self::prepareRecycleBinData($tableData, $table, $prefix);
                $systemRecycleBinService = Container::make(SystemRecycleBinService::class);
                $systemRecycleBinService->save($data);
            }
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
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
     * 设置创建人部门
     *
     * @param \support\Model $model
     */
    private static function setCreatedDept(Model $model): void
    {
        // 提前返回优化：当不满足基础条件时直接退出
        if (!$currentUser = getCurrentUser(true)) {
            return;
        }

        if (!$model->isFillable('created_dept')) {
            return;
        }

        // 安全获取部门ID（支持null安全访问）
        $deptId = $currentUser['dept_id'] ?? null;

        // 使用null合并运算符简化条件判断
        if ($deptId !== null) {
            $model->setAttribute('created_dept', $deptId);
        }
    }

    /**
     * 设置创建数据的租户编号
     *
     * @param \support\Model $model
     */
    private static function setTenantId(Model $model): void
    {
        if ($model instanceof SystemTenant) {
            return;
        }
        // 提前返回优化：当不满足基础条件时直接退出
        if (!$currentUser = getCurrentUser(true)) {
            return;
        }

        if (!$model->isFillable('tenant_id')) {
            return;
        }

        if(!empty($model->getAttribute('tenant_id'))){
            //这里要排除外面直接写入租户编号部分
            return;
        }

        // 安全获取部门ID（支持null安全访问）
        $tenantId = $currentUser['tenant_id'] ?? null;

        // 使用null合并运算符简化条件判断
        if ($tenantId !== null) {
            $model->setAttribute('tenant_id', $tenantId);
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
     *  实例化雪花算法
     *
     * @return Snowflake
     */
    private static function createSnowflake(): Snowflake
    {
        if (self::$snowflake == null) {
            self::$snowflake = new Snowflake(self::WORKER_ID, self::DATA_CENTER_ID);
        }
        return self::$snowflake;
    }

    /**
     * 生成雪花ID
     *
     * @return int
     * @throws \Exception
     */
    private static function generateSnowflakeID(): int
    {
        $snowflake = self::createSnowflake();
        return $snowflake->nextId();
    }

    private static function shouldStoreInRecycleBin($table): bool
    {
        return config('app.store_in_recycle_bin') && !in_array($table, config('app.exclude_from_recycle_bin'));
    }

    private static function prepareRecycleBinData($tableData, $table, $prefix): array
    {
        return [
            'data'         => json_encode($tableData),
            'table_name'   => $table,
            'table_prefix' => $prefix,
            'enabled'      => 0,
            'ip'           => request()->getRealIp(),
            'operate_id'   => getCurrentUser(),
        ];
    }

}
