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

/**
 * 用户角色验证器
 */
class SystemPostValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'code'    => 'require|alphaNum',
        'name'    => 'require|max:16',
        'sort'    => 'number',
        'enabled' => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'code.require'  => '职位标识必须填写',
        'code.alphaNum' => '职位标识只能由英文字或者数字母组成',
        'name.require'  => '职位名称必须填写',
        'name.max'      => '职位名称最多不能超过16个字符',
        'enabled'       => '状态必须填写',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'  => [
            'code',
            'name',
            'sort',
            'enabled',
        ],
        'update' => [
            'code',
            'name',
            'sort',
            'enabled',
        ],
    ];

}
