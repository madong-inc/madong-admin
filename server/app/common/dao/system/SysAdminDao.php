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

use app\common\model\system\SysAdmin;
use InvalidArgumentException;
use core\abstract\BaseDao;

class SysAdminDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysAdmin::class;
    }

    /**
     * 用户详情
     *
     * @param                   $id
     * @param array|null        $field
     * @param array|null        $with
     * @param string            $order
     * @param array|null        $withoutScopes
     *
     * @return SysAdmin|null
     * @throws \Exception
     */
    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null): SysAdmin|null
    {
        $result = $this->getModel()
            ->where('id', $id)
            ->with(['depts', 'posts', 'casbin.roles'])
            ->first()
            ->makeHidden(['password']);

        if ($result) {
            $result->role_id_list = [];
            $result->post_id_list = [];
            $result->dept_id      = '';

            // 处理 casbin 关系
            if ($result->casbin) {
                $roleIds = $result->casbin->flatMap(function ($item) {
                    return $item->roles->pluck('id');
                })->unique()->values()->all();

                $result->role_id_list = $roleIds;
            }

            // 处理 posts 关系
            if ($result->posts) {
                $result->post_id_list = $result->posts->pluck('id')->all();
            }

            // 处理 depts 关系
            if ($result->depts && $result->depts->isNotEmpty()) {
                $result->dept_id = $result->depts->first()->id;
            }
        }

        return $result;
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
     * @return array
     * @throws \Exception
     */
    public function getList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): array
    {
        // 初始化部门ID
        $deptId = null;

        // 遍历$where数组，查找并移除dept_id条件
        foreach ($where as $index => $condition) {
            // 条件可能是索引数组，第一个元素是字段名
            if (is_array($condition) && count($condition) >= 2) {
                $fieldName = $condition[0];
                // 如果字段名是'dept_id'
                if ($fieldName === 'dept_id') {
                    // 根据条件数组的长度判断操作符和值的位置
                    if (count($condition) === 2) {
                        // 条件格式为 ['dept_id', 值]
                        $deptId = $condition[1];
                    } elseif (count($condition) === 3) {
                        // 条件格式为 ['dept_id', '=', 值]
                        $deptId = $condition[2];
                    }
                    // 移除这个条件
                    unset($where[$index]);
                    // 因为我们只处理一个dept_id条件，所以找到后跳出循环
                    break;
                }
            }
        }

        // 重新索引数组，防止父类处理时出错
        $where = array_values($where);

        if (empty($with)) {
            $with = ['depts', 'posts', 'casbin.roles'];
        }

        $query = parent::selectModel($where, $field, $page, $limit, $order, $with, $search, $withoutScopes);

        // 如果存在deptId条件，则添加关联条件
        if (!is_null($deptId)) {
            $query->whereHas('depts', function ($q) use ($deptId) {
                $q->where('id', $deptId); // 假设部门模型的主键是id
            });
        }

        $total = $query->count();
        $items = $query->get()->makeHidden(['password', 'backend_setting']);
        return [$total, $items];
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
        $where['enabled']  = 1;//有效用户
        $where['is_super'] = 0;//非顶级管理员

        $query = $this->getModel()->with(['roles'])
            ->whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
            });

        if (!empty($where)) {
            unset($where['role_id']);
            $query->where($where);
        }

        $total = $query->count();

        $items = $query->when($page > 0 && $limit > 0, function ($query) use ($page, $limit) {
            return $query->skip(($page - 1) * $limit)->take($limit);
        })
            ->select($field)
            ->get()
            ->toArray();

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
     * @throws \Exception
     */
    public function getUsersExcludingRole(array $where, string $field, int $page, int $limit): array
    {
        $roleId = $where['role_id'] ?? null;
        if (!$roleId) {
            throw new InvalidArgumentException("Role ID is required.");
        }

        $where['enabled']  = 1;//有效用户
        $where['is_super'] = 0;//非顶级管理员

        // 获取排除的用户ID列表
        $sysAdminRoleDao  = new SysAdminRoleDao();
        $excludedAdminIds = $sysAdminRoleDao->getColumn(['role_id' => $roleId], 'admin_id');

        // 查询构建器
        $query = $this->getModel()->whereNotIn('id', $excludedAdminIds)
            ->with(['roles']);

        // 如果有额外的条件，则添加到查询中
        if (!empty($where)) {
            unset($where['role_id']);
            $query->where($where);
        }

        $total = $query->count();

        // 如果没有用户，则返回空结果
        if ($total === 0) {
            return ['total' => 0, 'items' => []];
        }

        $items = $query->when($page > 0 && $limit > 0, function ($query) use ($page, $limit) {
            return $query->skip(($page - 1) * $limit)->take($limit);
        })->select($field)->get()->toArray();

        return compact('total', 'items');
    }

    /**
     * @param string|int $id
     *
     * @return \app\common\model\system\SysAdmin|null
     * @throws \Exception
     */
    public function getAdminById(string|int $id): ?SysAdmin
    {
        $result = $this->getModel()
            ->where('id', $id)
            ->with(['depts', 'posts', 'casbin.roles', 'roles'])
            ->first()
            ->makeHidden(['password', 'backend_setting']);

        if ($result) {
            $result->role_id_list = [];
            $result->post_id_list = [];
            $result->dept_id      = '';

            // 处理 casbin 关系
            if ($result->casbin) {
                $roleIds = $result->casbin->flatMap(function ($item) {
                    return $item->roles->pluck('id');
                })->unique()->values()->all();

                $result->role_id_list = $roleIds;
            }

            // 处理 posts 关系
            if ($result->posts) {
                $result->post_id_list = $result->posts->pluck('id')->all();
            }

            // 处理 depts 关系
            if ($result->depts && $result->depts->isNotEmpty()) {
                $result->dept_id = $result->depts->first()->id;
            }
        }

        return $result;
    }

    /**
     * 获取用户详情
     *
     * @param string $name
     *
     * @return \app\common\model\system\SysAdmin|null
     * @throws \Exception
     */
    public function getAdminByName(string $name): ?SysAdmin
    {
        return $this->getModel()
            ->where('user_name', $name)
            ->with(['depts', 'posts', 'casbin.roles'])
            ->first();
    }

}
