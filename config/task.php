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

use madong\services\scheduler\event\EvalTask;
use madong\services\scheduler\event\SchedulingTask;
use madong\services\scheduler\event\ShellTask;
use madong\services\scheduler\event\UrlTask;

return [
    'debug'       => config('app.debug'),
    'enable'      => true,// 是否启用定时器  修改此参数后，需要重启
    'write_log'   => true,
    'listen'      => '127.0.0.1:' . '2346',// 注意此端口用于任务通讯，一个项目一个端口，请勿占用
    'task_handle' => [
        //任务操作类
        1 => UrlTask::class,
        2 => EvalTask::class,
        3 => ShellTask::class,
        4 => SchedulingTask::class,
    ],
];
