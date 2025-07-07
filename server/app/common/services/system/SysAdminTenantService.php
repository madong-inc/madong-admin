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

use app\common\dao\system\SysAdminTenantDao;
use app\common\model\system\SysAdminTenant;
use app\common\scopes\global\TenantScope;
use app\common\services\platform\TenantService;
use Exception;
use InvalidArgumentException;
use madong\admin\abstract\BaseService;
use madong\admin\ex\AdminException;
use support\Container;

/**
 * 管理员-租户会话
 *
 * @author Mr.April
 * @since  1.0
 */
class SysAdminTenantService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SysAdminTenantDao::class);
    }

    /**
     * 添加-用户关联租户
     *
     * @param array $data
     *
     * @return \app\common\model\system\SysAdminTenant
     */
    public function save(array $data): SysAdminTenant
    {
        try {
            return $this->transaction(function () use ($data) {
                //中间表不会自动追加手动处理一下
                $data['created_at'] = time();
                $data['updated_at'] = time();
                return $this->dao->save($data);
            });
        } catch (\Throwable $e) {
            // 记录日志或添加更多上下文信息到异常中
            throw new AdminException("Failed to update user: {$e->getMessage()}");
        }
    }

    /**
     * 获取租户列表
     *
     * @return \app\common\model\platform\Tenant|null
     */
    public function getTenantsNoScope(): ?\Illuminate\Database\Eloquent\Collection
    {
        $service = Container::make(TenantService::class);
        return $service->getModel()->withoutGlobalScope(TenantScope::class)->get();
    }

    /**
     * 获取租户部门
     *
     * @param string|int|null $tenantId
     *
     * @return \app\common\model\system\SysDept|null
     */
    public function getTenantDeptsNoScope(string|int|null $tenantId): ?\Illuminate\Database\Eloquent\Collection
    {
        $service = Container::make(SysDeptService::class);
        return $service->getModel()->withoutGlobalScope(TenantScope::class)->where('tenant_id', $tenantId)->get();
    }

    /**
     * 获取职位数据
     *
     * @param string|int|null $tenantId
     * @param string|int|null $deptId
     *
     * @return \app\common\model\system\SysPost|null
     */
    public function getTenantPostsNoScope(string|int|null $tenantId, string|int|null $deptId): ?\Illuminate\Database\Eloquent\Collection
    {
        $service = Container::make(SysPostService::class);
        return $service->getModel()->withoutGlobalScope(TenantScope::class)->where('tenant_id', $tenantId)->where('dept_id', $deptId)->get();
    }

    /**
     * 获取角色数据
     *
     * @param string|int|null $tenantId
     *
     * @return \app\common\model\system\SysRole|null
     */
    public function getTenantRolesNoScope(string|int|null $tenantId): ?\Illuminate\Database\Eloquent\Collection
    {
        $service = Container::make(SysRoleService::class);
        return $service->getModel()->withoutGlobalScope(TenantScope::class)->where('tenant_id', $tenantId)->get();
    }

    /**
     * 更新管理员与租户的关联关系
     *
     * @param string|int $admin_id   管理员ID
     * @param array      $tenantData 租户数据数组，格式为 [
     *                               'tenant_id' => ['is_super' => 1|2, 'is_default' => 1|0, 'priority' => int],
     *                               ...
     *                               ]
     *
     * @return void 操作是否成功
     * @throws \Exception
     */
    public function updateAdminTenantRelations(string|int $admin_id, array $tenantData = []): void
    {
        // 1. 验证数据格式是否有效
        if (empty($admin_id)) {
            throw new InvalidArgumentException('Invalid admin_id');
        }

        // 2. 如果没有传入 tenantData，则删除该 admin_id 下的所有关联
        if (empty($tenantData)) {
            $this->dao->getModel()->where('admin_id', $admin_id)->delete();
            return;
        }

        // 3. 验证 tenantData 的格式是否正确
        if (!$this->validateTenantData($tenantData)) {
            throw new \InvalidArgumentException('租户数据格式错误');
        }

        try {
            // 4. 获取当前数据库中已存在的关联记录，以 tenant_id 为键，id 为值
            $existingRelations = $this->dao->getModel()
                ->withoutGlobalScope('TenantScope')
                ->where('admin_id', $admin_id)
                ->get(['id', 'tenant_id'])
                ->keyBy('tenant_id')
                ->toArray();

            // 转换为 [tenant_id => id] 的映射，方便快速查找
            $existingTenantIdsMap = array_flip(array_column($existingRelations, 'tenant_id', 'id'));
            $existingTenantIds    = array_keys($existingTenantIdsMap); // 所有已存在的 tenant_id

            // 当前传入的 tenant_id 列表
            $newTenantIds = array_keys($tenantData);

            // 5. 找出需要删除的 tenant_id（存在于数据库但不在传入数据中）
            $obsoleteTenantIds = array_diff($existingTenantIds, $newTenantIds);
            if (!empty($obsoleteTenantIds)) {
                $this->dao->getModel()
                    ->withoutGlobalScope('TenantScope')
                    ->whereIn('tenant_id', $obsoleteTenantIds)
                    ->where('admin_id', $admin_id)
                    ->delete();
            }

            // 6. 遍历传入的 tenantData，逐个更新或插入
            foreach ($tenantData as $tenant_id => $data) {
                $is_super   = $data['is_super'] ?? 2;       // 默认值 2
                $is_default = $data['is_default'] ?? 0;   // 默认值 0
                $priority   = $data['priority'] ?? 0;       // 默认值 0

                if (isset($existingTenantIdsMap[$tenant_id])) {
                    // 存在记录，执行更新
                    $this->dao->getModel()
                        ->withoutGlobalScope('TenantScope')
                        ->where('tenant_id', $tenant_id)
                        ->where('admin_id', $admin_id)
                        ->update([
                            'is_super'   => $is_super,
                            'is_default' => $is_default,
                            'priority'   => $priority,
                        ]);
                } else {
                    // 不存在记录，执行插入
                    $this->dao->save([
                        'admin_id'   => $admin_id,
                        'tenant_id'  => $tenant_id,
                        'is_super'   => $is_super,
                        'is_default' => $is_default,
                        'priority'   => $priority,
                    ]);
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 验证 tenantData 数据格式是否合法
     *
     * @param array $tenantData
     *
     * @return bool
     */
    private function validateTenantData(array $tenantData): bool
    {
        foreach ($tenantData as $tenant_id => $data) {
            if (!isset($data['is_super'], $data['is_default'], $data['priority'])) {
                return false;
            }

            // 校验 is_super 是否为 1 或 2
            if (!in_array($data['is_super'], [1, 2])) {
                return false;
            }

            // 校验 is_default 是否为 0 或 1
            if (!in_array($data['is_default'], [0, 1])) {
                return false;
            }

            // 校验 priority 是否为整数
            if (!is_int($data['priority'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * 根据 tenant_id 数组构建默认的 tenantData 数据结构
     *
     * @param array $tenantIds tenant_id 数组
     *
     * @return array 构造好的 tenantData 数据结构
     */
    public function buildDefaultTenantData(array $tenantIds): array
    {
        $tenantData = [];

        foreach ($tenantIds as $tenant_id) {
            // 默认值设置
            $tenantData[$tenant_id] = [
                'is_super'   => 2,    // 默认非超级管理员
                'is_default' => 0,    // 默认非默认租户
                'priority'   => 0,    // 默认优先级为 0
            ];
        }

        return $tenantData;
    }
}
