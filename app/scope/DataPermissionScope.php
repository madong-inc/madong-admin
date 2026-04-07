<?php

/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\scope;

use app\adminapi\CurrentUser;
use app\enum\system\DataPermission;
use app\service\admin\org\DeptService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use support\Container;

/**
 * 数据权限Scope类
 *
 * @author Mr.April
 * @since  1.0
 */
class DataPermissionScope implements Scope
{
    /**
     * 数据权限类型
     *
     * @var array
     */
    protected array $dataScopes = [];

    /**
     * 权限数据列表
     *
     * @var array
     */
    protected array $dataAuths = [];

    /**
     * 当前用户ID
     *
     * @var int|string|null
     */
    protected null|int|string $currentUserId = null;

    /**
     * 是否为超级管理员
     *
     * @var bool
     */
    protected bool $isSuperAdmin = false;

    public function apply(Builder $builder, Model $model): void
    {
        $this->initPermissionData();

        // 超级管理员跳过处理
        if ($this->isSuperAdmin) {
            return;
        }

        // 非全部权限时处理
        if (!in_array(DataPermission::ALL->value, $this->dataScopes)) {
            $this->applyDataAuthority($builder, $model);
        }
    }

    /**
     * 初始化权限数据
     */
    protected function initPermissionData(): void
    {

        /** @var CurrentUser $currentUser */
        $currentUser = Container::make(CurrentUser::class);
        $admin       = $currentUser->admin();

        if (empty($admin)) {
            return;
        }

        $this->currentUserId = $admin->id ?? 0;
        $this->isSuperAdmin  = $admin->isSuperAdmin();

        // 如果是超级管理员，直接返回
        if ($this->isSuperAdmin) {
            $this->dataScopes = [DataPermission::ALL->value];
            return;
        }

        $roles = $admin->roles;

        // 合并所有角色的数据权限
        $this->mergeRolePermissions($roles);
    }

    /**
     * 合并多个角色的权限
     */
    protected function mergeRolePermissions($roles): void
    {
        $allScopes  = [];
        $allDeptIds = [];

        foreach ($roles as $role) {
            // 获取角色的数据权限类型
            $scope = $role->data_scope ?? null;
            if (empty($scope)) {
                $scope = DataPermission::ALL->value;
            }
            $allScopes[] = $scope;

            // 获取角色的数据权限部门
            if ($scope == DataPermission::CUSTOM->value) {
                $deptIds    = $role->scopes()->pluck('id')->toArray();
                $allDeptIds = array_merge($allDeptIds, $deptIds);
            } elseif ($scope == DataPermission::CURRENT_DEPT->value) {
                $deptIds    = $role->depts()->pluck('id')->toArray();
                $allDeptIds = array_merge($allDeptIds, $deptIds);
            } elseif ($scope == DataPermission::CURRENT_DEPT_WITH_CHILDREN->value) {
                // 实现获取部门及子部门的逻辑
                $deptIds    = $this->getDeptAndChildrenIds($role->depts()->pluck('id')->toArray());
                $allDeptIds = array_merge($allDeptIds, $deptIds);
            }
        }

        // 去重
        $this->dataScopes = array_unique($allScopes);
        $this->dataAuths  = array_unique($allDeptIds);

        // 如果有任何一个角色有全部权限，则用户拥有全部权限
        if (in_array(DataPermission::ALL->value, $this->dataScopes)) {
            $this->dataScopes = [DataPermission::ALL->value];
            $this->dataAuths  = [];
        }
    }

    /**
     * 获取部门及所有子部门ID
     *
     * @param array $deptIds
     *
     * @return array
     */
    protected function getDeptAndChildrenIds(array $deptIds): array
    {
        if (empty($deptIds)) {
            return [];
        }

        // 使用DeptService获取部门及子部门ID
        /** @var DeptService $deptService */
        $deptService = Container::make(DeptService::class);
        $allDeptIds  = [];

        foreach ($deptIds as $deptId) {
            // 调用DeptService的方法获取部门及子部门ID
            $childDeptIds = $deptService->getChildIdsIncludingSelf($deptId);
            $allDeptIds   = array_merge($allDeptIds, $childDeptIds);
        }

        return array_unique($allDeptIds);
    }

    /**
     * 应用数据权限
     *
     * @param Builder $builder
     * @param Model   $model
     */
    protected function applyDataAuthority(Builder $builder, Model $model): void
    {
        $hasDeptField      = $model->isFillable('dept_id');
        $hasCreatedByField = $model->isFillable('created_by'); // 检查模型是否有created_by字段

        // 只有当模型既没有dept_id字段也没有created_by字段时，才跳过权限检查
        if (!$hasDeptField && !$hasCreatedByField) {
            return;
        }

        // 构建查询条件
        $builder->where(function ($query) use ($model, $hasDeptField, $hasCreatedByField) {
            $hasCondition = false;

            foreach ($this->dataScopes as $scope) {
                switch ($scope) {
                    case DataPermission::CUSTOM->value:
                    case DataPermission::CURRENT_DEPT->value:
                    case DataPermission::CURRENT_DEPT_WITH_CHILDREN->value:
                        // 只有当模型有dept_id字段且有权限数据时，才应用部门权限过滤
                        if ($hasDeptField && !empty($this->dataAuths)) {
                            $query->orWhereIn('dept_id', $this->dataAuths);
                            $hasCondition = true;
                        }
                        break;

                    case DataPermission::SELF->value:
                        // 只有当模型有created_by字段且当前用户ID存在时，才应用创建者权限过滤
                        if ($hasCreatedByField && $this->currentUserId) {
                            $query->orWhere('created_by', $this->currentUserId);
                            $hasCondition = true;
                        }
                        break;

                    case DataPermission::HYBRID->value:
                        // 部门权限过滤（只有当模型有dept_id字段且有权限数据时）
                        if ($hasDeptField && !empty($this->dataAuths)) {
                            $query->orWhereIn('dept_id', $this->dataAuths);
                            $hasCondition = true;
                        }
                        // 创建者权限过滤（只有当模型有created_by字段且当前用户ID存在时）
                        if ($hasCreatedByField && $this->currentUserId) {
                            $query->orWhere('created_by', $this->currentUserId);
                            $hasCondition = true;
                        }
                        break;
                }
            }

            // 如果没有任何条件，默认限制查询（防止无权限时查询全部数据）
            if (!$hasCondition) {
                $query->whereRaw('1=0');
            }
        });
    }

    private function getToken(): ?string
    {
        $request = request();
        if (empty($request)) {
            return null;
        }
        $tokenName     = config('core.jwt.app.token_name', 'Authorization');
        $authorization = $request->header($tokenName);
        if (empty($authorization) || $authorization === 'undefined') {
            $authorization = $request->get('token');
        }
        if (!$authorization || $authorization === 'undefined') {
            return null;
        }
        if (count(explode(' ', $authorization)) !== 2) {
            return null;
        }

        [$type, $token] = explode(' ', $authorization);

        if ($type !== 'Bearer') {
            return null;
        }

        if (!$token || $token === 'undefined') {
            return null;
        }

        return $token;
    }
}