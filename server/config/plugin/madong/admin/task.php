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

use madong\admin\services\scheduler\event\EvalTask;
use madong\admin\services\scheduler\event\SchedulingTask;
use madong\admin\services\scheduler\event\ShellTask;
use madong\admin\services\scheduler\event\UrlTask;

return [
    'debug'       => config('app.debug'),
    'enabled'     => env('APP_TASK_ENABLED', false),// 是否启用定时器  修改此参数后，需要重启
    'write_log'   => false,
    'listen'      => '127.0.0.1:' . '2337',// 注意此端口用于任务通讯，一个项目一个端口，请勿占用
    'task_handle' => [
        //任务操作类
        \app\common\enum\system\TaskScheduleType::TASK_URL->value      => UrlTask::class,
        \app\common\enum\system\TaskScheduleType::TASK_EVAL->value     => EvalTask::class,
        \app\common\enum\system\TaskScheduleType::TASK_SHELL->value    => ShellTask::class,
        \app\common\enum\system\TaskScheduleType::TASK_SCHEDULE->value => SchedulingTask::class,
    ],
];
