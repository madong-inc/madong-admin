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

namespace app\common\services\system;

use app\common\dao\system\SysRoleDao;
use app\common\model\system\SysRole;
use core\abstract\BaseService;
use core\casbin\Permission;
use core\enum\system\PolicyPrefix;
use core\exception\handler\AdminException;
use madong\helper\Arr;
use madong\helper\PropertyCopier;
use support\Container;

class SysRoleService extends BaseService
{

    /**
     * 缓存key
     */
    const CACHE_ALL_ROLES_DATA = 'roles_all_data';

    public function __construct()
    {
        $this->dao = Container::make(SysRoleDao::class);
    }

    /**
     * 获取所有的角色
     *
     * @param array $where
     *
     * @return array
     */
    public function getAllRoles(array $where = []): array
    {
        // 1. 优先从缓存获取所有角色数据
        $allRoles = $this->cacheDriver()->remember(
            self::CACHE_ALL_ROLES_DATA, // 缓存键名
            function () {
                $results = $this->dao->getModel()
                    ->where('enabled', 1)
                    ->orderBy('sort', 'asc')
                    ->get(["id",
                           "pid",
                           "name",
                           "code",
                           "is_super_admin",
                           "role_type",
                           "data_scope",
                           "enabled",
                           "sort"]);

                return $results->toArray();
            }
        );
        // 2.$where 条件筛数据
        return Arr::filterByWhere($allRoles, $where);
    }

    /**
     * 传入角色id获取权限
     *
     * @param array  $where
     * @param string $mode merge|unique
     *
     * @return array
     */
    public function getPermissions(array $where = [], string $mode = 'merge'): array
    {
        // 1. 优先从缓存获取所有全量角色数据
        $data = $this->cacheDriver()->remember(
            self::CACHE_ALL_ROLES_DATA, // 缓存键名
            function () {
                return $this->dao->getRolePermissions();
            }
        );
        //2. 条件过滤
        $result = Arr::filterByWhere($data, $where);
        if ($mode == 'unique') {
            return $menu ?? [];
        }
        return [
            'menu'   => array_merge([], ...array_column($result, 'menu')),
            'api'    => array_merge([], ...array_column($result, 'api')),
            'button' => array_merge([], ...array_column($result, 'button')),
        ];
    }

    /**
     * 通过角色id 获取casbin策略的权限IDS
     *
     * @param int|string  $id
     * @param string|null $tenantId
     *
     * @return array
     */
    public function getPermissionColumns(int|string $id, string|null $tenantId = null): array
    {
        $roleId   = PolicyPrefix::ROLE->value . $id;
        $domain   = '*';
        $result   = Permission::getImplicitResourcesForUser($roleId, $domain);
        if (empty($result)) {
            return [];
        }
        // 提取并返回所需的列
        return array_map(function ($item) {
            return str_replace('menu:', '', $item);
        }, array_column($result, 5));
    }

    /**
     * save
     *
     * @param array $data
     *
     * @return SysRole|null
     * @throws \core\exception\handler\AdminException
     */
    public function save(array $data): SysRole|null
    {
        try {
            return $this->transaction(function () use ($data) {
                $menus = $data['permissions'] ?? [];
                $model = $this->dao->save($data);

                // 格式化用户标识符，用于 Casbin
                $roleId = PolicyPrefix::ROLE->value . strval($model->id);
                $domain = '*';

                // 获取当前用户在 Casbin 中的隐式资源权限

                $currentPolicies = Permission::getImplicitResourcesForUser($roleId, $domain);


                // 提取完整的当前权限（0-5）
                $currentPermissions = array_map(
                    function ($policy) {
                        return $policy; // 保留完整的策略数组
                    },
                    $currentPolicies
                );

                // 将传入的菜单数据转换为 Casbin 策略格式
                $menuService   = new SysMenuService();
                $policyService = new SysCasbinService();
                $menusData     = $menuService->getAllMenus(['id' => $menus], 'menu', false);
                // 构建传入的权限列表，格式与当前权限相同
                $incomingPolicies = [];
                foreach ($menusData as $item) {
                    // 确定路径：如果类型是按钮，则使用 code，否则使用 path
                    $path = $policyService->resolvePermissionIdentifier($item);

                    // 构建策略数组
                    $policy = [
                        $roleId,        // sub
                        $domain,          // dom
                        $path,            // obj
                        '*',              // act（根据你的业务需求调整）
                        $item['methods'], // method
                        (string)PolicyPrefix::MENU->value . $item['id'], // trace_id
                    ];
                    // 保留完整的策略数组
                    $incomingPolicies[] = $policy;
                }
                // 更新 Casbin 权限
                $policyService->updateCasbinPolicies($currentPermissions, $incomingPolicies);
                // 同步 Casbin 权限，确保只有授权角色保留
                $model->casbin()->sync([$roleId]);
                return $model;
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 编辑
     *
     * @param $id
     * @param $data
     *
     * @return void
     * @throws \core\exception\handler\AdminException
     */
    public function update($id, $data): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                // 获取传入的权限列表（如果存在）
                $menus = $data['permissions'] ?? [];
                // 从数据访问对象中获取模型实例
                $model = $this->dao->get($id);
                if (!$model) {
                    return;
                }
                // 使用属性复制器将传入的数据复制到模型中
                PropertyCopier::copyProperties((object)$data, $model);
                $model->save();

                // 格式化用户标识符，用于 Casbin
                $roleId = PolicyPrefix::ROLE->value . strval($model->id);
                $domain = '*';

                // 获取当前用户在 Casbin 中的隐式资源权限
                $currentPolicies = Permission::getImplicitResourcesForUser($roleId, $domain);

                // 提取完整的当前权限（0-5）
                $currentPermissions = array_map(
                    function ($policy) {
                        return $policy; // 保留完整的策略数组
                    },
                    $currentPolicies
                );

                // 将传入的菜单数据转换为 Casbin 策略格式
                $menuService   = new SysMenuService();
                $policyService = new SysCasbinService();

                $menusData = $menuService->getAllMenus(['id' => $menus], 'menu', false);
                // 构建传入的权限列表，格式与当前权限相同
                $incomingPolicies = [];
                foreach ($menusData as $item) {
                    $path = $policyService->resolvePermissionIdentifier($item);
                    // 构建策略数组
                    $policy = [
                        $roleId,                                 // sub
                        $domain,                                 // dom
                        $path,                                   // obj
                        '*',                                     // act（根据你的业务需求调整）
                        $item['methods'],                        // method
                        PolicyPrefix::MENU->value . $item['id'], // trace_id 资源归属用于追踪
                    ];
                    // 保留完整的策略数组
                    $incomingPolicies[] = $policy;
                }
                // 更新 Casbin 权限
                $policyService->updateCasbinPolicies($currentPermissions, $incomingPolicies);
                // 同步 Casbin 权限，确保只有授权角色保留
                $model->casbin()->sync([$roleId]);
            });
        } catch (\Throwable $e) {
            // 捕获异常并抛出自定义异常
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 删除角色
     *
     * @param array $id
     * @param bool  $force
     *
     * @return mixed
     * @throws \core\exception\handler\AdminException
     */
    public function destroy(array $id, bool $force = false): mixed
    {
        try {
            return $this->transaction(function () use ($id, $force) {
                $data       = Arr::normalize($id);
                $deletedIds = [];
                $domain     = '*';
                foreach ($data as $id) {
                    $item = $this->get($id);
                    if ($item) {
                        $primaryKey = $item->getPk();
                        $item->delete();
                        $deletedIds[] = $item->{$primaryKey};

                        // 移除租户下所有角色的策略
                        $roleId = PolicyPrefix::ROLE->value . strval($item->{$primaryKey});
                        Permission::removeFilteredPolicy(0, $roleId, $domain, '', '', '', '');
                        $item->casbin()->sync([]);
                    }
                }
                return $deletedIds;
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 分配数据权限
     *
     * @param $id
     * @param $data
     *
     * @throws \core\exception\handler\AdminException
     */
    public function updateScope($id, $data): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                $scope = $data['permissions'] ?? [];
                $model = $this->dao->get($id);
                PropertyCopier::copyProperties((object)$data, $model);
                if (!empty($model)) {
                    $model->save();
                    if ($model->data_scope !== 2) {
                        $scope = [];
                    }
                    $model->scopes()->sync($scope);
                }
                $this->cacheDriver()->delete(self::CACHE_ALL_ROLES_DATA);
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
