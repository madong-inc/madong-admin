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

use app\common\dao\system\SysAdminDao;
use core\abstract\BaseService;
use core\casbin\Permission;
use core\enum\system\PolicyPrefix;
use madong\interface\IDict;
use support\Container;

class SysAuthService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SysAdminDao::class);
    }

    /**
     * 获取菜单
     *
     * @param \madong\interface\IDict $dict
     * @param bool                    $includeButtons
     *
     * @return array
     */
    public function getMenusByUserRoles(IDict $dict, bool $includeButtons = false): array
    {
        $isSuperAdmin = boolval($dict->get('is_super', 0));
        $menuService  = new SysMenuService();
        $map1         = $includeButtons ? ['type' => [1, 2, 3, 4]] : ['type' => [1, 2]];
        if ($isSuperAdmin) {
            // 顶级管理员返回全部菜单
            return $menuService->getAllMenus($map1);
        }
        $userId   = $dict->get('id');
        $userName = 'user:' . $userId;
        $domain   = '*';

        // 普通成员 - 按角色分配权限
        $userPermissions = Permission::getImplicitPermissionsForUser($userName, $domain);
        // 过滤出包含菜单权限
        $userFilteredPermissions = array_filter($userPermissions, function ($item) use ($includeButtons) {
            if (!$includeButtons) {
                return isset($item[2]) && str_starts_with($item[2], PolicyPrefix::MENU->value);
            }
            return true;
        });
        $userResultValues        = array_map(function ($item) {
            return str_replace(PolicyPrefix::MENU->value, '', $item);
        }, array_column($userFilteredPermissions, 5));

        return $this->getMenusByIds($menuService, array_unique($userResultValues), $includeButtons);

    }

    /**
     * 根据IDS输出菜单
     *
     * @param \app\common\services\system\SysMenuService $menuService
     * @param array                                      $ids
     * @param bool                                       $includeButtons
     *
     * @return array
     */
    private function getMenusByIds(SysMenuService $menuService, array $ids, bool $includeButtons = false): array
    {
        $map1 = $includeButtons ? ['type' => [1, 2, 3, 4]] : ['type' => [1, 2]];
        if (empty($ids)) {
            return [];
        }
        $map1['id'] = $ids;
        return $menuService->getAllMenus($map1) ?? [];
    }

    /**
     * 获取用户角色-权限码
     *
     * @param \madong\interface\IDict $dict
     *
     * @return array
     */
    public function getCodesByUserRoles(IDict $dict): array
    {
        $isSuperAdmin = boolval($dict->get('is_super', 0));
        if ($isSuperAdmin) {
            // 顶级管理员
            return ['admin'];
        }

        $userName = 'user:' . $dict->get('id');
        $domain   = '*';
        // 普通成员 - 按角色分配权限
        $userPermissions = Permission::getImplicitPermissionsForUser($userName, $domain);
        // 提取权限并仅保留带有 button 前缀的权限，去掉前缀
        $permissions = array_filter($userPermissions, function ($item) {
            return isset($item[2]) && str_starts_with($item[2], PolicyPrefix::BUTTON->value);
        });
        // 去掉前缀并返回去重结果
        $cleanedPermissions = array_map(function ($item) {
            return str_replace(PolicyPrefix::BUTTON->value, '', $item[2]); // 确保安全访问
        }, $permissions);

        // 去重并返回结果
        $resultValues = array_values(array_unique($cleanedPermissions));

        // 排序并返回结果
        sort($resultValues);
        return $resultValues ?? [];
    }
}
