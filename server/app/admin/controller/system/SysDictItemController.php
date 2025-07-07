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
use app\admin\validate\system\SysDictItemValidate;
use app\common\services\system\SysDictItemService;
use support\Container;

class SysDictItemController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SysDictItemService::class);
        $this->validate = Container::make(SysDictItemValidate::class);
    }

}
