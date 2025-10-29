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

use app\common\model\system\SysAdmin;
use think\Validate;

class SysAdminValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'user_name'    => 'require|max:18|unique',
        'real_name'    => 'require',
        'password'     => 'require|min:5|max:18',
        'dept_id'      => 'require',
        'mobile_phone' => 'require|mobile',
        'old_password' => 'require',
        'new_password' => 'require|min:5|max:18',
        'id'           => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'           => '缺少参数id',
        'user_name.require'    => '用户名必须填写',
        'user_name.max'        => '用户名最多不能超过18个字符',
        'user_name.unique'     => '用户名已被占用',
        'real_name.require'    => '姓名必须填写',
        'password.require'     => '密码必须填写',
        'password.min'         => '密码最少为5位',
        'password.max'         => '密码长度不能超过18位',
        'old_password.require' => '旧密码不能为空',
        'new_password.require' => '密码必须填写',
        'new_password.min'     => '密码最少为5位',
        'new_password.max'     => '密码长度不能超过18位',
        'dept_id'              => '部门必须填写',
        'mobile_phone.require' => '手机号码必须填写',
        'mobile_phone.mobile'  => '无效手机号码',
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
        $query = SysAdmin::where('user_name', $value);
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
        'store'              => [
            'user_name',
            'password',
            'dept_id',
            'mobile_phone',
        ],
        'update'             => [
            'user_name',
            'dept_id',
            'mobile_phone',
        ],
        'update-info'        => [
            'real_name',
            'mobile_phone',
            'sex',
        ],
        'update-pwd'         => [
            'new_password',
            'old_password',
        ],
        'update-preferences' => [
            'id',
        ],
    ];
}
