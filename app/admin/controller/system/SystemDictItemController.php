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
use app\admin\validate\system\SystemDictItemValidate;
use app\services\system\SystemDictItemService;
use support\Container;

class SystemDictItemController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemDictItemService::class);
        $this->validate = Container::make(SystemDictItemValidate::class);
    }

}
