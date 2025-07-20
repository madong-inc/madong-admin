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

namespace app\common\dao\platform;

use app\common\model\platform\TenantPackage;
use core\abstract\BaseDao;

/**
 * 租户订阅套餐
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantPackageDao extends BaseDao
{

    protected function setModel(): string
    {
        return TenantPackage::class;
    }

    /**
     * 根据订阅ID获取租户ID列表（带作用域控制）
     *
     * @param int  $subscriptionId
     * @param bool $withTrashed 是否包含软删除数据
     *
     * @return array
     * @throws \Exception
     */
    public function getTenantIdsBySubscription(int $subscriptionId, bool $withTrashed = false): array
    {
        return $this->getModel()
            ->when($withTrashed, function ($query) {
                $query->withoutGlobalScopes();
            })
            ->where('subscription_id', $subscriptionId)
            ->pluck('tenant_id')
            ->toArray();
    }

    /**
     * 根据租户ID获取订阅ID列表（带作用域控制）
     *
     * @param int  $tenantId
     * @param bool $withTrashed
     *
     * @return array
     * @throws \Exception
     */
    public function getSubscriptionByTenantIds(int $tenantId, bool $withTrashed = false): array
    {
        return $this->getModel()
            ->when($withTrashed, function ($query) {
                $query->withoutGlobalScopes();
            })
            ->where('tenant_id', $tenantId)
            ->pluck('subscription_id')
            ->toArray();
    }

}
