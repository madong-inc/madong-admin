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

class SysAuthValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'  => [

        ],
        'update' => [

        ],
    ];
}
