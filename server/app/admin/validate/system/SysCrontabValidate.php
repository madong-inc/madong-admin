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

class SysCrontabValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'data'    => 'require|task_enabled',
        'title'   => 'require|task_enabled',
        'type'    => 'require',
        'rule'    => 'require',
        'target'  => 'require',
        'enabled' => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'data.require'       => '唯一标识ID不能为空',
        'data.task_enabled'  => '定时任务未开启,.env文件APP_TASK_ENABLED=true',
        'title.require'      => '任务名称必须填写',
        'title.task_enabled' => '定时任务未开启,.env文件APP_TASK_ENABLED=true',
        'type.require'       => '任务类型必须填写',
        'rule.require'       => '任务规则必须填写',
        'target.require'     => '调用目标必须填写',
        'enabled.require'    => '任务状态必须填写',
    ];

    /**
     * 验证是否启动定时任务
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function task_enabled($value, $rule, array $data = []): bool
    {
        return config('plugin.madong.admin.task.enabled', false);
    }

    /**
     * 定义场景
     */
    protected $scene = [
        'start'   => [
            'data',
        ],
        'resume'  => [
            'data',
        ],
        'pause'   => [
            'data',
        ],
        'execute' => [
            'data',
        ],
        'destroy' => [
            'data',
        ],
        'store'   => [
            'title',
            'type',
            'target',
            'enabled',
        ],
        'update'  => [
            'title',
            'type',
            'rule',
            'target',
            'enabled',
        ],
    ];
}
