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

use app\common\dao\system\SystemUserRoleDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;

/**
 * @author Mr.April
 * @since  1.0
 */
class SystemUserRoleService extends BaseService
{


    public function __construct(SystemUserRoleDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 更新设置角色权限
     *
     * @param $data
     */
    public function save($data): void
    {
        try {
            $this->transaction(function () use ($data) {
                $userId   = $data['user_id'] ?? '';
                $newRoles = $data['role_id_list'] ?? [];
                if (empty($userId)) {
                    throw new AdminException('参数错误缺少user_id', -1);
                } // 获取当前权限
                $currentRoles = $this->getColumn(['user_id' => $userId], 'role_id');

                // 计算需要添加和删除的权限
                $userRoleIdsToAdd    = array_diff($newRoles, $currentRoles);
                $userRoleIdsToRemove = array_diff($currentRoles, $newRoles);

                // 批量删除权限
                if (!empty($userRoleIdsToRemove)) {
                    $this->dao->delete([
                        ['role_id', 'in', $userRoleIdsToRemove],
                        ['user_id', '=', $userId],
                    ]);
                }

                // 批量添加权限
                if (!empty($userRoleIdsToAdd)) {
                    $data = array_map(function ($roleId) use ($userId) {
                        return ['role_id' => $roleId, 'user_id' => $userId];
                    }, $userRoleIdsToAdd);
                    $this->dao->saveAll($data);
                }
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 移除用户-关联角色
     *
     * @param array $data
     */
    public function removeUserRole(array $data)
    {
        try {
            $this->transaction(function () use ($data) {
                foreach ($data as $item) {
                    $this->dao->getModel()->where($item)->delete();
                }
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 保存用户-关联角色
     *
     * @param array $data
     *
     * @return void
     */
    public function saveUserRoles(array $data): void
    {
        try {
            $this->transaction(function () use ($data) {
                $this->dao->saveAll($data);
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
