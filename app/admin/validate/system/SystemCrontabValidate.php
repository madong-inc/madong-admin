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

class SystemCrontabValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'      => 'require',
        'title'   => 'require',
        'type'    => 'require',
        'rule'    => 'require',
        'target'  => 'require',
        'enabled' => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'      => '唯一标识ID不能为空',
        'title.require'   => '任务名称必须填写',
        'type.require'    => '任务类型必须填写',
        'rule.require'    => '任务规则必须填写',
        'target.require'  => '调用目标必须填写',
        'enabled.require' => '任务状态必须填写',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'start'   => [
            'id',
        ],
        'resume'  => [
            'id',
        ],
        'pause'   => [
            'id',
        ],
        'execute' => [
            'id',
        ],
        'destroy' => [
            'id',
        ],
        'store'   => [
            'title',
            'type',
            'rule',
            'target',
            'status',
        ],
        'update'  => [
            'title',
            'type',
            'rule',
            'target',
            'status',
        ],
    ];
}
