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

namespace app\common\scopes\global;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use core\context\TenantContext;
use support\Db;

/**
 * 租户-分库模式字段字段隔离作用域
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSharedTableScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        // 获取当前模型对应的数据库表名
        $tableName = $model->getTable();

        // 检查表中是否存在 tenant_id 字段
        if (!Db::Schema()->hasColumn($tableName, 'tenant_id')) {
            return;
        }
        if (config('tenant.enabled', false) && TenantContext::isLibraryIsolation()) {
            $builder->where("tenant_id", TenantContext::getTenantId());
        }
    }

}
