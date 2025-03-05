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

namespace app\dao\system;

use app\model\system\SystemUser;
use InvalidArgumentException;
use madong\basic\BaseDao;

/**
 * @method getModel()
 */
class SystemUserDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemUser::class;
    }

    /**
     * 获取用户列表-角色id
     *
     * @param array  $where
     * @param string $field
     * @param int    $page
     * @param int    $limit
     *
     * @return array
     */
    public function getUsersListByRoleId(array $where, string $field, int $page, int $limit): array
    {
        $roleId = $where['role_id'] ?? null;
        if (!$roleId) {
            throw new InvalidArgumentException("Role ID is required.");
        }
        $query = $this->getModel()->whereHas('roles', function ($query) use ($roleId) {
            $query->where('id', $roleId);
        });
        if (!empty($where)) {
            unset($where['role_id']);
            $query->where($where);
        }
        $total = $query->count();
        $items = $query->when($page > 0 && $limit > 0, function ($query) use ($page, $limit) {
            return $query->skip(($page - 1) * $limit)->take($limit);
        })->select($field)->get()->toArray();
        return compact('total', 'items');
    }

    /**
     * 排除角色ID-用户列表
     *
     * @param array  $where
     * @param string $field
     * @param int    $page
     * @param int    $limit
     *
     * @return array
     */
    public function getUsersExcludingRole(array $where, string $field, int $page, int $limit): array
    {
        $roleId = $where['role_id'] ?? null;
        if (!$roleId) {
            throw new InvalidArgumentException("Role ID is required.");
        }

        //获取排除的用户ID列表
        $systemUserRoleDao = new SystemUserRoleDao();
        $excludedUserIds   = $systemUserRoleDao->getColumn(['role_id' => $roleId], 'user_id');

        // 查询构建器
        $query = $this->getModel()->whereNotIn('id', $excludedUserIds)
            ->with('roles'); // 假设你希望在结果中加载角色关系

        // 如果有额外的条件，则添加到查询中
        if (!empty($where)) {
            unset($where['role_id']);
            $query->where($where);
        }

        $total = $query->count();

        $items = $query->when($page > 0 && $limit > 0, function ($query) use ($page, $limit) {
            return $query->skip(($page - 1) * $limit)->take($limit);
        })->select($field)->get()->toArray();

        return compact('total', 'items');
    }

}
