<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitcode.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\common\scopes\global;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use madong\exception\AuthException;
use support\Db;

/**
 * 租户
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        // 获取当前模型对应的数据库表名
        $tableName = $model->getTable();

        // 检查表中是否存在 tenant_id 字段
        if (!Db::Schema()->hasColumn($tableName, 'tenant_id')) {
            return;
        }

        $request  = request();
        $tenantId = $request->getTenantId();
        if (config('app.tenant_enabled', false) && empty($tenantId)) {
            throw new AuthException('网络异常请稍后重试');
        }

        // 检查是否启用租户功能并且 tenant_id 不为空
        if (config('app.tenant_enabled', false)) {
            $builder->where('tenant_id', $tenantId);
        }
    }

}
