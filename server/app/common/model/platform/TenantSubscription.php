<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */

namespace app\common\model\platform;

use app\common\model\system\SysMenu;
use madong\admin\abstract\BaseModel;
use madong\admin\context\TenantContext;
use madong\casbin\model\RuleModel;

/**
 * 租户套餐模型
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSubscription extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'mt_tenant_subscription';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        "id",
        "name",
        "description",
        "sort",
        "start_time",
        "end_time",
        "enabled",
        "created_at",
        "updated_at",
        "remark",
    ];

//    public function menus(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(SysMenu::class, TenantSubscriptionMenu::class, 'subscription_id', 'menu_id');
//    }

    public function tenants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, TenantPackage::class, 'subscription_id', 'tenant_id');
    }

    /**
     * 关联策略表
     */
    public function casbin(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        $tenantId = config('tenant.enabled', false) ? 'domain:' . TenantContext::getTenantId() : 'default';
        return $this->belongsToMany(RuleModel::class, TenantSubscriptionCasbin::class, 'subscription_id', 'subscription_casbin_id', 'id', 'v0');
//            ->where('v2', $tenantId);
    }

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
