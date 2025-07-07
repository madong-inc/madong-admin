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

namespace app\admin\validate\platform;

use think\Validate;

class TenantSubscriptionValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'        => 'require',
        'tenant_id' => 'require',
        'name'      => 'require',
        'enabled'   => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'          => '参数ID不能为空',
        'tenant_id.require'   => '租户id不能为空',
        'name.require'        => '名称不能为空',
        'description.require' => '描述不能为空',
        'enabled.require'     => '状态不能为空',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'            => [
            'name',
        ],
        'update'           => [
            'id',
        ],
        'destroy'          => [],
        'grant_permission' => [
            'id',
        ],
        'grant_tenant'     => [
            'id',
        ],
    ];
}
