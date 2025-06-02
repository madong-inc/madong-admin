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
 * 菜单验证器
 */
class SystemMenuValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'pid'     => 'require',
        'code'    => 'require',
        'title'   => 'require|max:16',
        'type'    => 'number',
        'sort'    => 'number',
        'enabled' => 'number',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'pid.require'   => '菜单上级必须填写',
        'code.require'  => '菜单标识必须填写',
        'title.require' => '菜单名称必须填写',
        'title.max'     => '菜单名称最多不能超过16个字符',
        'enabled'       => '状态必须填写',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'       => [
            'code',
            'title',
            'type',
            'sort',
            'enabled',
        ],
        'update'      => [
            'code',
            'title',
            'type',
            'sort',
            'enabled',
        ],
        'batch-store' => [
            'title',
            'type',
            'sort',
        ],
    ];

}
