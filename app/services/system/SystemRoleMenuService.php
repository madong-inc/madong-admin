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
namespace app\services\system;

use app\dao\system\SystemRoleMenuDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;
use think\db\Query;
use think\facade\Db;

/**
 * @method getColumn(int[]|string[] $array, string $string)
 */
class SystemRoleMenuService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemRoleMenuDao::class);
    }

    /**
     * 获取角色权限集合
     * @param int|string $roleId
     *
     * @return array
     */
    public function rolePermission(int|string $roleId): array
    {
        try {
            return $this->getColumn(['role_id' => $roleId], 'menu_id');
        } catch (AdminException $e) {
            return [];
        }
    }

    /**
     * 更新设置角色权限
     *
     * @param int|string $roleId
     * @param array      $newPermissions
     */
    public function updateRolePermission(int|string $roleId, array $newPermissions): void
    {
        Db::startTrans();
        try {
            // 获取当前权限
            $currentPermissions = $this->getColumn(['role_id' => $roleId], 'menu_id');

            // 计算需要添加和删除的权限
            $roleMenuIdsToAdd    = array_diff($newPermissions, $currentPermissions);
            $roleMenuIdsToRemove = array_diff($currentPermissions, $newPermissions);

            // 批量删除权限
            if (!empty($roleMenuIdsToRemove)) {
                $this->dao->delete([
                    ['menu_id', 'in', $roleMenuIdsToRemove],
                    ['role_id', '=', $roleId],
                ]);
            }

            // 批量添加权限
            if (!empty($roleMenuIdsToAdd)) {
                $data = array_map(function ($menuId) use ($roleId) {
                    return ['role_id' => $roleId, 'menu_id' => $menuId];
                }, $roleMenuIdsToAdd);

                $this->dao->saveAll($data);
            }

            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw new AdminException($e->getMessage());
        }
    }
}
