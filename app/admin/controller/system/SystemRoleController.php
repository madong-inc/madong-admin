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
use app\admin\validate\system\SystemRoleValidate;
use app\services\system\SystemRoleService;
use support\Container;

class SystemRoleController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemRoleService::class);
        $this->validate = Container::make(SystemRoleValidate::class);
    }
}
