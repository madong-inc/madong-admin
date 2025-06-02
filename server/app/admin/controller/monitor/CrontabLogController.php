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

namespace app\admin\controller\monitor;

use app\admin\controller\Crud;
use app\common\services\system\SystemCrontabLogService;
use support\Container;

/**
 *
 * 定时任务日志
 * @author Mr.April
 * @since  1.0
 */
class CrontabLogController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
        $this->service  = Container::make(SystemCrontabLogService::class);
    }

}
