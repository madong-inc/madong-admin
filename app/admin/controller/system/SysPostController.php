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
use app\admin\validate\system\SysPostValidate;
use app\common\services\system\SysPostService;
use support\Container;

class SysPostController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SysPostService::class);
        $this->validate = Container::make(SysPostValidate::class);
    }

}
