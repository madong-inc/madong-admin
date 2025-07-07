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

namespace app\common\services\platform;

use app\common\dao\platform\TenantPackageDao;
use app\common\enum\system\PolicyPrefix;
use InvalidArgumentException;
use madong\admin\abstract\BaseService;
use madong\casbin\Permission;
use support\Container;

/**
 * 租户套餐
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantPackageService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(TenantPackageDao::class);
    }

    /**
     * 返回套餐资源IDS
     *
     * @param int|string $tenantId
     *
     * @return array
     */
    public function getTenantPackagePermissions(int|string $tenantId): array
    {
        // 获取租户套餐
        $package = $this->dao->getModel()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->first();

        // 检查套餐是否存在
        if (!$package || empty($package->subscription_id)) {
            throw new InvalidArgumentException("Tenant not found or has no subscription for ID: $tenantId");
        }

        // 获取订阅 ID
        $subId = PolicyPrefix::SUBSCRIPTION->value . $package->subscription_id;

        // 获取隐式权限
        $result = Permission::getImplicitPermissionsForUser($subId, '*');

        // 如果没有结果，返回空数组
        if (empty($result)) {
            return [];
        }

        // 提取权限并去掉前缀
        $permissions = array_map(function ($item) {
            return str_replace(PolicyPrefix::MENU->value, '', $item[5] ?? ''); // 确保安全访问
        }, $result);

        // 去重并返回结果
        return array_values(array_unique($permissions));
    }

    /**
     * 返回套餐按钮权限Codes
     *
     * @param int|string $tenantId
     *
     * @return array
     */
    public function getTenantPackagePermissionsCodes(int|string $tenantId): array
    {
        // 获取租户套餐
        $package = $this->dao->getModel()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->first();

        // 检查套餐是否存在
        if (!$package || empty($package->subscription_id)) {
            throw new InvalidArgumentException("Tenant not found or has no subscription for ID: $tenantId");
        }

        // 获取订阅 ID
        $subId = PolicyPrefix::SUBSCRIPTION->value . $package->subscription_id;

        // 获取隐式权限
        $result = Permission::getImplicitPermissionsForUser($subId, '*');

        // 如果没有结果，返回空数组
        if (empty($result)) {
            return [];
        }

        // 提取权限并仅保留带有 button 前缀的权限，去掉前缀
        $permissions = array_filter($result, function ($item) {
            return isset($item[2]) && str_starts_with($item[2], PolicyPrefix::BUTTON->value);
        });

        // 去掉前缀并返回去重结果
        $cleanedPermissions = array_map(function ($item) {
            return str_replace(PolicyPrefix::BUTTON->value, '', $item[2]); // 确保安全访问
        }, $permissions);

        // 去重并返回结果
        return array_values(array_unique($cleanedPermissions));
    }

}
