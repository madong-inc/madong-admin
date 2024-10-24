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

class SystemDictItemValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'label'   => 'require',
        'value'   => 'require',
        'enable'  => 'require',
        'type_id' => 'require',
        'code'    => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'label'   => '字典名称必须填写',
        'value'   => '字典标识必须填写',
        'enable'  => '状态必须填写',
        'type_id' => '字典类型必须填写',
        'code'    => '字典标识必须填写',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'   => [
            'label',
            'value',
            'enable',
            'type_id',
            'code',
        ],
        'update' => [
            'label',
            'value',
            'enable',
            'type_id',
            'code',
        ],
    ];

}