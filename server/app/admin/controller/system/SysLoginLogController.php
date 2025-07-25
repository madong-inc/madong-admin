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
use app\common\services\system\SysLoginLogService;
use support\Container;

/**
 *
 * 登录日志
 * @author Mr.April
 * @since  1.0
 */
class SysLoginLogController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SysLoginLogService::class);
    }
}
