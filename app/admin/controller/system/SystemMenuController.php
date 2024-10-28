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
use app\admin\validate\system\SystemMenuValidate;
use app\services\system\SystemMenuService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemMenuController extends Crud
{

    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemMenuService::class);
        $this->validate = Container::make(SystemMenuValidate::class);
    }

}
