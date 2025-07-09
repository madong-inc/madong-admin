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

use app\common\dao\platform\TenantSubscriptionDao;
use app\common\enum\system\MenuType;
use app\common\enum\system\PolicyPrefix;
use app\common\model\platform\TenantSubscription;
use app\common\services\system\SysCasbinService;
use app\common\services\system\SysMenuService;
use InvalidArgumentException;
use LogicException;
use madong\admin\abstract\BaseService;
use madong\admin\context\TenantContext;
use madong\admin\ex\AdminException;
use madong\casbin\Permission;
use support\Container;

/**
 * 租户套餐
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSubscriptionService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(TenantSubscriptionDao::class);
    }

    /**
     * 通过角色id 获取casbin策略的权限IDS
     *
     * @param int|string $id
     * @param bool       $includeImplicitResources
     *
     * @return array
     */
    public function getPermissionColumns(int|string $id, bool $includeImplicitResources = false): array
    {
        $tenantId = config('tenant.enabled', false) ? TenantContext::getTenantId() : 'default';

        $subId  = PolicyPrefix::SUBSCRIPTION->value . strval($id);
        $domain = PolicyPrefix::DOMAIN->value . strval($tenantId);

        // 根据 $includeImplicitResources 的值选择调用的方法
        $result = $includeImplicitResources
            ? Permission::getImplicitResourcesForUser($subId, $domain)
            : Permission::getPermissionsForUser($subId);

        // 提取并返回所需的列
        return array_map(function ($item) {
            return str_replace('menu:', '', $item);
        }, array_column($result, 5));
    }

    /**
     * 删除订阅套餐and删除关联数据
     *
     * @param array|int|string $data
     *
     * @return mixed
     * @throws \Throwable
     */
    public function serviceDestroy(array|int|string $data): mixed
    {
        return $this->transaction(function () use ($data) {
            $data       = is_array($data) ? $data : explode(',', $data);
            $deletedIds = [];
            foreach ($data as $id) {
                $item = $this->get($id);
                if (!$item) {
                    continue; // 如果找不到项，跳过
                }
                $item->delete();
                $item->menus()->detach();//同步删除中间表
                $item->tenants()->detach();//同步删除中间表
                $primaryKey   = $item->getPk();
                $deletedIds[] = $item->{$primaryKey};
            }
            return $deletedIds;
        });
    }

    /**
     * 授权权限
     *
     * @throws \Exception|\Throwable
     */
    public function serviceGrantPermission(string|int $id, array $data = []): ?TenantSubscription
    {
        try {
            return $this->transaction(function () use ($id, $data) {
                $model = $this->dao->getModel()->findOrFail($id);
                $menus = $data ?? [];
                // 格式化用户标识符，用于 Casbin
                $subId  = PolicyPrefix::SUBSCRIPTION->value . strval($model->id);
                $domain = '*';

                // 获取当前用户在 Casbin 中的隐式资源权限
                $currentPolicies = Permission::getImplicitResourcesForUser($subId, $domain);

                // 提取完整的当前权限（0-5）
                $currentPermissions = array_map(
                    function ($policy) {
                        return $policy; // 保留完整的策略数组
                    },
                    $currentPolicies
                );

                // 将传入的菜单数据转换为 Casbin 策略格式
                $menuService   = new SysMenuService();
                $casbinService = new SysCasbinService();
                $menusData     = $menuService->getAllMenus(['id' => $menus], 'menu', false);
                // 构建传入的权限列表，格式与当前权限相同
                $incomingPolicies = [];
                foreach ($menusData as $item) {
                    // 确定路径：如果类型是按钮，则使用 code，否则使用 path
                    $path = $casbinService->resolvePermissionIdentifier($item);
                    // 构建策略数组
                    $policy = [
                        $subId,           // sub
                        $domain,          // dom
                        $path,            // obj
                        '*',              // act（根据你的业务需求调整）
                        $item['methods'], // method
                        PolicyPrefix::MENU->value . $item['id'], // trace_id 资源归属用于追踪
                    ];
                    // 保留完整的策略数组
                    $incomingPolicies[] = $policy;
                }
                // 更新 Casbin 权限
                $casbinService->updateCasbinPolicies($currentPermissions, $incomingPolicies);
                // 同步 Casbin 权限，确保只有授权保留
                $model->casbin()->sync([$subId]);
                return $model;
            });
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * 关联租户
     *
     * @param string|int $id
     * @param array      $data
     *
     * @return \app\common\model\platform\TenantSubscription|null
     * @throws \Throwable
     */
    public function serviceGrantTenant(string|int $id, array $data): ?TenantSubscription
    {
        try {
            return $this->transaction(function () use ($id, $data) {
                $model = $this->dao->getModel()->findOrFail($id);
                $model->tenants()->detach();
                $model->tenants()->sync($data);
                return $model;
            });
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

}
