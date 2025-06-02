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

use think\Validate;

class SystemRoleValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'         => 'require',
        'code'       => 'require|alpha',
        'name'       => 'require|max:16',
        'data_scope' => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'         => '参数id不能为空',
        'code.require'       => '角色标识必须填写',
        'code.alpha'         => '角色标识只能由英文字母组成',
        'name.require'       => '角色名称必须填写',
        'name.max'           => '角色名称最多不能超过16个字符',
        'data_scope.require' => '数据权限不能为空',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'      => [
            'code',
            'name',
        ],
        'update'     => [
            'id',
            'code',
            'name',
        ],
        'data-scope' => [
            'id',
            'data_scope',
        ],
    ];
}
