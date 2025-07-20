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

use app\common\model\platform\Tenant;
use core\abstract\BaseDao;

/**
 * 账套中心
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantDao extends BaseDao
{

    protected function setModel(): string
    {
        return Tenant::class;
    }

    /**
     * 获取租户套餐关联的菜单IDs
     *
     * @param int $tenantId 租户ID
     *
     * @return array 菜单ID数组
     * @throws \Exception
     */
//    public function getTenantPackageMenuIds(int $tenantId): array
//    {
//        return $this->getModel()->with(['packages.menus'])
//            ->findOrFail($tenantId)
//            ->packages
//            ->flatMap(function($package) {
//                return $package->menus->pluck('id');
//            })
//            ->unique()
//            ->values()
//            ->toArray();
//    }

}
