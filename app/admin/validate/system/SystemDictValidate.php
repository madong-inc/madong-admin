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

class SystemDictValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'name'   => 'require|max:16',
        'code'   => 'require|alphaDash',
        'enabled' => 'require',
        'sort'   => 'number',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'name.require'   => '字典名称必须填写',
        'name.max'       => '字典名称最多不能超过16个字符',
        'code.require'   => '字典标识必须填写',
        'code.alphaDash' => '字典标识只能由英文字母组成',
        'enabled'         => '状态必须填写',
        'sort.number'    => '排序只能是数字',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'  => [
            'name',
            'code',
            'enabled',
            'sort',
        ],
        'update' => [
            'name',
            'code',
            'enabled',
            'sort',
        ],
    ];

}