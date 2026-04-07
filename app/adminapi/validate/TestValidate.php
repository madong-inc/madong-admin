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

namespace app\adminapi\validate;

use core\base\BaseValidate;

class TestValidate extends BaseValidate
{
    /**
     * 场景.
     *
     * @var \string[][]
     */
    protected array $scene = [
        'login'    => [
            'account',
            'password',
            'captcha',
        ],
        'save'     => [
            'account',
            'password',
            'avatar',
            'real_name',
            'roles',
        ],
        'update'   => [
            'account',
            'avatar',
            'real_name',
            'roles',
        ],
        'password' => [
            'password',
            'password_confirm',
        ],
    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'account'          => 'required|min:5|max:50|alpha_num',
            'password'         => [
                'required',
                'min:5',
                'max:50',
            ],
            'captcha'          => 'required',
            'password_confirm' => [
                'required',
                'password_confirm_api:' . input('password'),
            ],
            'avatar'           => 'required',
            'real_name'        => 'required',
            'roles'            => 'required',
        ];
    }

    /**
     * 设置错误提醒.
     *
     * @return string[]
     */
    public function message(): array
    {
        return [
            'account.required'                      => '账号必须填写',
            'account.min'                           => '账号长度不正确',
            'account.max'                           => '账号长度超出限制',
            'account.alpha_num'                     => '账号不正确',
            'password.required'                     => '密码必须填写',
            'password.min'                          => '密码长度不正确,最少5个字符',
            'password.max'                          => '密码长度不正确',
            'password.regex'                        => '输入的密码不符合规则,请输入的组合',
            'password_confirm.password_confirm_api' => '两次输入的密码不正确',
            'password_confirm.required'             => '请填写确认密码',
            'captcha.required'                      => '验证码必须填写',
            'captcha.captcha_api'                   => '验证码不正确',
            'avatar.required'                       => '请选择头像',
            'real_name.required'                    => '请填写管理员姓名',
            'roles.required'                        => '请选择管理员身份',
        ];
    }
}
