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

use app\adminapi\CurrentUser;
use app\dao\system\AdminDao;
use core\base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use support\Container;

class AuthService extends BaseService
{

    public function __construct(AdminDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取菜单
     *
     * @param \app\adminapi\CurrentUser $currentUser
     * @param bool                      $includeButtons
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function getMenusByUserRoles(CurrentUser $currentUser, bool $includeButtons = false): ?Collection
    {
        $adminModel   = $currentUser->admin();
        if (!$adminModel) {
            return new Collection();
        }
        
        $isSuperAdmin = boolval($adminModel->getAttribute('is_super'));
        /** @var MenuService $menuService */
        $menuService = Container::make(MenuService::class);
        $map1        = $includeButtons ? ['type' => [1, 2, 3, 4]] : ['type' => [1, 2]];
        
        if ($isSuperAdmin) {
            // 顶级管理员返回全部菜单
            return $menuService->selectList($map1, '*', 0, 0, 'sort', [], true);
        }
        
        // 普通成员 - 通过角色获取菜单
        $menuIds = $this->getUserMenuIds($adminModel);
        return $this->getMenusByIds($menuService, array_unique($menuIds), $includeButtons);
    }
    
    /**
     * 获取用户拥有的菜单ID列表
     *
     * @param \app\model\system\Admin $adminModel
     * @return array 菜单ID数组
     */
    private function getUserMenuIds($adminModel): array
    {
        $menuIds = [];
        
        // 通过角色获取菜单
        $roles = $adminModel->roles()->with('menus')->get();
        
        foreach ($roles as $role) {
            foreach ($role->menus as $menu) {
                $menuIds[] = $menu->id;
            }
        }
        
        return array_unique($menuIds);
    }

    /**
     * 根据IDS输出菜单
     *
     * @param \app\service\admin\system\MenuService $menuService
     * @param array                              $ids
     * @param bool                               $includeButtons
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    private function getMenusByIds(MenuService $menuService, array $ids, bool $includeButtons = false): Collection
    {
        if (empty($ids)) {
            return new Collection();
        }
        $menuModel  = $menuService->dao->getModel();
        $chunkSize  = 200;
        $chunks     = array_chunk($ids, $chunkSize);
        $typeFilter = function ($query) use ($includeButtons) {
            $types = $includeButtons ? [1, 2, 3, 4] : [1, 2];
            $query->whereIn('type', $types);
        };

        // 分块查询 + 批量合并（减少内存占用）
        $allResults = new Collection();
        foreach ($chunks as $chunk) {
            $results    = $menuModel
                ->whereIn('id', $chunk)
                ->where('enabled', 1)
                ->where($typeFilter)
                ->orderBy('sort')
                ->get();
            $allResults = $allResults->merge($results);
        }
        return $allResults;
    }

    /**
     * 获取用户角色-权限码
     *
     * @param \app\adminapi\CurrentUser $currentUser
     *
     * @return array
     */
    public function getCodesByUserRoles(CurrentUser $currentUser): array
    {
        $adminModel   = $currentUser->admin();
        if (!$adminModel) {
            return [];
        }
        
        $isSuperAdmin = boolval($adminModel->getAttribute('is_super'));
        if ($isSuperAdmin) {
            // 顶级管理员
            return ['admin'];
        }

        // 普通成员 - 通过角色获取权限码
        return $currentUser->getPermissions();
    }
}
