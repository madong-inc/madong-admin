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

namespace app\common\dao\system;

use app\common\model\system\SystemTenant;
use app\common\scopes\global\AccessScope;
use app\common\scopes\global\TenantScope;
use madong\basic\BaseDao;

/**
 * 租户
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemTenantDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemTenant::class;
    }

    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Model
    {
        return parent::get($id, $field, $with, $order, [TenantScope::class, AccessScope::class]);
    }
}
