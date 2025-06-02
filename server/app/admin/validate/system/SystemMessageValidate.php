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

class SystemMessageValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'      => 'require',
        'title'   => 'require',
        'content' => 'require',
        'status'  => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'      => '参数错误缺少id',
        'title.require'   => '公告名称必须填写',
        'content.require' => '公共内容不能为空',
        'status'          => '状态必须填写',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'store'  => [
            'title',
            'type',
            'content',
        ],
        'update' => [
            'id',
            'title',
            'type',
            'content',
        ],
        'update-read'=>[
            'id',
            'status'
        ]
    ];

}
