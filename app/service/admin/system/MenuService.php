<?php
declare(strict_types=1);
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

namespace app\service\admin\system;

use app\dao\system\MenuDao;
use app\model\system\Menu;
use core\base\BaseService;
use core\exception\handler\AdminException;
use madong\helper\Arr;
use madong\helper\Tree;
use support\Container;

/**
 * @method save(array $data)
 * @method selectModel(array $where, array|string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null)
 */
class MenuService extends BaseService
{

    /**
     * 缓存key
     */
    const CACHE_KEY = 'menu_list';

    /**
     * 所有权限
     */
    const CACHE_ALL_AUTHS_DATA = 'all_auths_data';

    /**
     * 所有菜单
     */
    const CACHE_ALL_MENUS_DATA = 'all_menus_data';

    public function __construct(MenuDao $dao)
    {

        $this->dao = $dao;
    }

    /**
     * 输出所有权限
     *
     * @param array $menuIds
     *
     * @return array
     * @throws \Exception
     */
    public function getAllAuth(array $menuIds = []): array
    {
        // 1. 优先从缓存获取所有权限数据（自动处理缓存未命中时的数据库查询）
        $allAuthItems = $this->cacheDriver()->remember(
            self::CACHE_ALL_AUTHS_DATA,
            function () {
                // 缓存未命中时，从数据库获取数据（仅查询需要的字段）
                return $this->dao->getModel()
                    ->where('path', '<>', '')
                    ->where('type', '=', 4)
                    ->get(['id', 'path', 'methods'])
                    ->toArray();
            }
        );

        // 2. 如果传入了 $menuIds，则筛选出对应的权限项
        if (!empty($menuIds)) {
            $allAuthItems = array_filter($allAuthItems, function ($item) use ($menuIds) {
                return in_array($item['id'], $menuIds);
            });
        }

        // 3. 格式化输出（复用现有方法）
        return $this->formatAuthDataFromItems($allAuthItems);
    }

    /**
     * 根据授权项数组格式化输出为 ['METHOD' => ['/path1', '/path2'], ...] 的形式。
     *
     * @param array $allAuthItems 授权项数组，每个元素包含 'id', 'path', 'methods'
     *
     * @return array 格式化后的授权数据
     */
    private function formatAuthDataFromItems(array $allAuthItems): array
    {
        $allAuth = [];
        foreach ($allAuthItems as $item) {
            // 假设 'methods' 是逗号分隔的字符串，如 "GET,POST"
            $methodArray = explode(',', $item['methods']); // 转换为数组

            $pathKey = strtolower(trim(str_replace(' ', '', $item['path'])));

            foreach ($methodArray as $method) {
                $methodKey             = strtolower(trim($method));
                $allAuth[$methodKey][] = $pathKey;
            }
        }

        // 如果需要，可以对每个方法的路径数组进行去重
        foreach ($allAuth as &$paths) {
            $paths = array_unique($paths);
        }
        unset($paths); // 解除引用
        return $allAuth;
    }

    /**
     * 获取所有菜单，优先从缓存读取，并根据 $where 条件筛选。
     *
     * @param array $where 筛选条件数组，键为字段名，值为条件值或数组（用于 IN 条件）或操作符数组（如 ['>', 5]）
     *
     * @return array 处理后的菜单树形结构
     */
//    public function getAllMenus(array $where = [], $mode = 'menu', $isTree = true): array
//    {
//        // 1. 优先从缓存获取所有菜单数据
//        $allMenus = $this->cacheDriver()->remember(
//            self::CACHE_ALL_MENUS_DATA, // 缓存键名
//            function () {
//                $list = $this->dao->getModel()
//                    ->where('enabled', 1)
////                    ->whereIn('type', [1, 2])// 目录 & 菜单类型 全量保存缓存条件放到缓存输出
//                    ->orderBy('sort', 'asc')
//                    ->get();
//                foreach ($list as $item) {
//                    $item->set('name', $item->code);
//                    $item->set('meta', SysMenu::getMetaAttribute($item));
//                }
//                $list->makeVisible(['id', 'pid', 'type', 'sort', 'redirect', 'path', 'name', 'meta', 'component']);
//                return $list->toArray();
//            }
//        );
//
//        // 2. 根据 $where 条件筛选菜单数据
//        $menusToProcess = Arr::filterByWhere($allMenus, $where);
//
//        if ($mode == 'code') {
//            return array_column($menusToProcess, 'code');
//        }
//        if (!$isTree) {
//            return $menusToProcess;
//        }
//
//        // 构建树形结构
//        return $this->buildMenuTree($menusToProcess);
//
//    }

    public function getAllMenus(array $where = [], $mode = 'menu', $isTree = true): array
    {
        // 1. 优先从缓存获取所有菜单数据
        $allMenus = $this->cacheDriver()->remember(
            self::CACHE_ALL_MENUS_DATA, // 缓存键名
            function () {
                return $this->dao->getModel()
                    ->where('enabled', 1)
//                    ->whereIn('type', [1, 2])// 目录 & 菜单类型 全量保存缓存条件放到缓存输出
                    ->orderBy('sort', 'asc')
                    ->get();
//                foreach ($list as $item) {
//                    $item->set('name', $item->code);
//                    $item->set('meta', SysMenu::getMetaAttribute($item));
//                }
//                $list->makeVisible(['id', 'pid', 'type', 'sort', 'redirect', 'path', 'name', 'meta', 'component']);
//                return $list->toArray();
            }
        );

        // 2. 根据 $where 条件筛选菜单数据
        $menusToProcess = Arr::filterByWhere($allMenus, $where);

        if ($mode == 'code') {
            return array_column($menusToProcess, 'code');
        }
        if (!$isTree) {
            return $menusToProcess;
        }

        // 构建树形结构
        return $this->buildMenuTree($menusToProcess);

    }

    /**
     * 构建菜单的树形结构。
     *
     * @param array $formattedMenus 已处理的菜单数据
     *
     * @return array 树形结构的菜单数据
     */
    protected function buildMenuTree(array $formattedMenus): array
    {
        $tree = new Tree($formattedMenus);
        return $tree->getTree();
    }



    /*** 重写方法***/

    /**
     * 重写更新方法：统一事务管理菜单更新、缓存清理、Casbin同步
     *
     * @param int|string $id   菜单ID
     * @param array      $data 更新数据
     *
     * @return \app\model\system\Menu 更新后的模型
     * @throws \Throwable
     * @throws \core\exception\handler\AdminException
     */
    public function update(int|string $id, array $data): \app\model\system\Menu
    {
        return $this->transaction(function () use ($id, $data) {
            // 1. 获取菜单模型
            $model = $this->get($id);
            if (!$model) {
                throw new AdminException("菜单ID:{$id}不存在");
            }

            // 2. 更新菜单数据（使用属性复制或直接赋值）
            $model->fill($data);
            $model->save();

            // 3. 清理缓存（原控制器中的缓存删除逻辑迁移至此）
            $this->cacheDriver()->delete(self::CACHE_ALL_AUTHS_DATA);
            $this->cacheDriver()->delete(self::CACHE_ALL_MENUS_DATA);

            // 4. 同步Casbin策略（复用已实现的同步方法）
            $this->syncPermissionCacheAfterUpdate($id);
            return $model;
        });
    }

    /**
     * 批量删除菜单（含子菜单）并同步删除Casbin策略
     *
     * @param array $data 菜单ID数组或逗号分隔字符串
     *
     * @return array 被删除的所有菜单ID
     * @throws \Throwable
     * @throws \core\exception\handler\AdminException
     */
    public function batchDelete(array $data = []): array
    {

        return $this->transaction(function () use ($data) {
            $deletedIds = [];

            foreach ($data as $id) {
                /** @var Menu $item */
                $item = $this->get($id);
                if (!$item) {
                    continue;
                }
                // 删除菜单及子菜单，获取所有被删除的ID
                $ids        = $item->deleteWithAllChildren();
                $deletedIds = array_merge($deletedIds, $ids);
            }

            // 清理角色菜单中间表关联数据
            if (!empty($deletedIds)) {
                /** @var RoleMenuService $roleMenuService */
                $roleMenuService = Container::make(RoleMenuService::class);
                /** @var RoleMenuModel $roleMenuModel */
                $roleMenuModel = $roleMenuService->getModel();
                $roleMenuModel->whereIn('menu_id', $deletedIds)->delete();
                
                // 清理相关用户的权限缓存
                $this->clearUserPermissionCacheByMenus($deletedIds);
            }

            return array_unique($deletedIds);
        });
    }

    /**
     * 菜单更新后清理相关用户权限缓存
     *
     * @param int|string $menuId 被更新的菜单ID
     *
     * @throws \core\exception\handler\AdminException
     */
    private function syncPermissionCacheAfterUpdate(int|string $menuId): void
    {
        try {
            // 1. 查询所有关联此菜单的角色ID（通过角色菜单服务）
            /** @var RoleMenuService $roleMenuService */
            $roleMenuService = Container::make(RoleMenuService::class);
            $roleIds         = $roleMenuService->getColumn(
                ['menu_id' => $menuId],
                'role_id'
            );
            if (empty($roleIds)) {
                return; // 无关联角色，无需同步
            }

            // 2. 清理拥有这些角色的用户的权限缓存
            foreach ($roleIds as $roleId) {
                /** @var \app\service\admin\system\AdminRoleService $adminRoleService */
                $adminRoleService = Container::make(\app\service\admin\system\AdminRoleService::class);
                
                // 获取拥有此角色的用户ID列表
                $userIds = \app\model\system\AdminRole::where('role_id', $roleId)
                    ->pluck('admin_id')
                    ->toArray();
                
                if (!empty($userIds)) {
                    // 清理这些用户的权限缓存
                    $currentUser = Container::make(\app\adminapi\CurrentUser::class);
                    foreach ($userIds as $userId) {
                        $currentUser->clearCache($userId);
                    }
                }
            }
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }
    
    /**
     * 清理与指定菜单相关的用户权限缓存
     *
     * @param array $menuIds 菜单ID数组
     */
    private function clearUserPermissionCacheByMenus(array $menuIds): void
    {
        try {
            if (empty($menuIds)) {
                return;
            }
            
            // 获取拥有这些菜单的角色ID
            /** @var RoleMenuService $roleMenuService */
            $roleMenuService = Container::make(RoleMenuService::class);
            /** @var RoleMenuModel $roleMenuModel */
            $roleMenuModel = $roleMenuService->getModel();
            
            $roleIds = $roleMenuModel->whereIn('menu_id', $menuIds)
                ->pluck('role_id')
                ->unique()
                ->toArray();
            
            if (!empty($roleIds)) {
                // 获取拥有这些角色的用户ID
                $userIds = \app\model\system\AdminRole::whereIn('role_id', $roleIds)
                    ->pluck('admin_id')
                    ->unique()
                    ->toArray();
                
                if (!empty($userIds)) {
                    // 清理这些用户的权限缓存
                    $currentUser = Container::make(\app\adminapi\CurrentUser::class);
                    foreach ($userIds as $userId) {
                        $currentUser->clearCache($userId);
                    }
                }
            }
        } catch (\Throwable $e) {
            // 缓存清理失败不影响主要业务，记录日志即可
            \core\logger\Logger::error("清理菜单相关用户权限缓存失败: " . $e->getMessage());
        }
    }

}
