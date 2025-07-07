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

namespace app\admin\validate\system;

use app\common\model\system\SysAdminTenant;
use think\Validate;

class SysAdminTenantValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'         => 'require',
        'admin_id'   => 'require',
        'tenant_id'  => 'require|unique',
        'is_super'   => 'require',
        'is_default' => 'require',
        'priority'   => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'         => '缺少参数id',
        'admin_id.require'   => '参数admin_id不能为空',
        'tenant_id.require'  => '参数tenant_id不能为空',
        'is_super.require'   => '请选择是否管理员',
        'is_default.require' => '请选择是否默认租户',
        'tenant_id.unique'   => '租户已设置请勿重复添加',
    ];

    /**
     * 用户名重复验证
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function unique($value, $rule, array $data = []): bool
    {
        $query = SysAdminTenant::withoutGlobalScope('TenantScope')->where('tenant_id', $value)->where('admin_id',$data['admin_id']);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 定义场景
     */
    protected $scene = [
        'store'  => [
            "admin_id",
            "tenant_id",
            "is_super",
        ],
        'update' => [
            'id',
            "admin_id",
            "tenant_id",
        ],
        'delete' => [
            'id',
        ],
    ];
}
