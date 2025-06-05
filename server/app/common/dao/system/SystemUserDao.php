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

namespace app\common\dao\system;

use app\common\model\system\SystemUser;
use app\common\scopes\global\AccessScope;
use InvalidArgumentException;
use madong\basic\BaseDao;

class SystemUserDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemUser::class;
    }

    /**
     * 用户详情
     *
     * @param            $id
     * @param array|null $field
     * @param array|null $with
     * @param string     $order
     * @param array|null $withoutScopes
     *
     * @return SystemUser|null
     * @throws \Exception
     */
    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null): SystemUser|null
    {
        $model = parent::get($id, ['*'], ['roles', 'posts', 'depts'], '', $withoutScopes);
        if (!empty($model)) {
            $roles = $model->getData('roles');
            $posts = $model->getData('posts');
            $model->set('role_id_list', []);
            $model->set('post_id_list', []);
            if (!empty($roles)) {
                $model->set('role_id_list', array_column($roles->toArray(), 'id'));
            }
            if (!empty($posts)) {
                $model->set('post_id_list', array_column($posts->toArray(), 'id'));
            }
            $model->makeHidden(['password']);
        }
        return $model;
    }

    /**
     * 获取用户列表
     *
     * @param array      $where
     * @param string     $field
     * @param int        $page
     * @param int        $limit
     * @param string     $order
     * @param array      $with
     * @param bool       $search
     * @param array|null $withoutScopes
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        return parent::selectList($where, $field, $page, $limit, $order, ['depts', 'posts', 'roles'], $search, $withoutScopes)->makeHidden(['password']);
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
     * @throws \Exception
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
     * @throws \ReflectionException
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
