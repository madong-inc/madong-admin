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

use app\dao\system\SystemUserRoleDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;
use think\facade\Db;

/**
 * @method save(array $data)
 */
class SystemUserRoleService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUserRoleDao::class);
    }

    /**
     * 角色分配用户
     *
     * @param string $roleId
     * @param array  $users
     */
    public function usersToRoleById(string $roleId, array $users): void
    {
        try {
            //1.0 获取当前角色已分配的$existingRoles用户
            $existingUsers = $this->getColumn(['role_id' => $roleId], 'user_id');
            $usersToAdd    = array_diff($users, $existingUsers);
            if (empty($usersToAdd)) {
                throw new AdminException('No new users to add.');
            }
            //2.0 增量添加用户关联角色
            $data = [];
            foreach ($usersToAdd as $userId) {
                $data[] = ['user_id' => $userId, 'role_id' => $roleId];
            }
            $this->saveAll($data);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 用户分配多个角色
     *
     * @param string $userId
     * @param array  $roles
     */
    public function assignUserToRolesById(string $userId, array $roles): void
    {
        try {
            //1.0 获取当前用户已分配的$existingRoles用户
            $existingRoles = $this->getColumn(['user_id' => $userId], 'role_id');
            $rolesToAdd    = array_diff($roles, $existingRoles);
            if (empty($usersToAdd)) {
                throw new AdminException('No new users to add.');
            }
            //2.0 增量添加用户关联角色
            $data = [];
            foreach ($rolesToAdd as $roleId) {
                $data[] = ['user_id' => $userId, 'role_id' => $roleId];
            }
            $this->saveAll($data);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 移除用户-关联角色
     *
     * @param string|int       $roleId
     * @param string|int|array $data
     */
    public function removeUserRole(string|int $roleId, string|int|array $data)
    {
        try {
            // 构建查询条件
            $map = [['role_id', '=', $roleId]];

            // 处理 $data
            if (is_string($data)) {
                $data = array_map('trim', explode(',', $data));
            }

            // 根据 $data 的类型构建条件
            if (is_array($data) && !empty($data)) {
                $map[] = ['user_id', 'in', $data];
            } elseif (!empty($data)) {
                $map[] = ['user_id', '=', $data];
            } else {
                throw new AdminException('无效的用户数据。');
            }

            // 执行删除操作
            $this->delete($map);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
