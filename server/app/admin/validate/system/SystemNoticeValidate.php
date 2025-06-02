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

class SystemNoticeValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'         => 'require',
        'message_id' => 'require',
        'title'      => 'require',
        'content'    => 'require',
        'enabled'    => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'         => '参数错误缺少id',
        'message_id.require' => '参数错误缺少message_id',
        'title.require'      => '公告名称必须填写',
        'content.require'    => '公共内容不能为空',
        'enabled'            => '状态必须填写',
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
    ];

}
