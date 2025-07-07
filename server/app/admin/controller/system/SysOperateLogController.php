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

namespace app\admin\controller\system;

use app\admin\controller\Crud;
use app\common\services\system\SysOperateLogService;
use support\Container;

/**
 * 行为日志
 *
 * @author Mr.April
 * @since  1.0
 */
class SysOperateLogController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SysOperateLogService::class);
    }

}
