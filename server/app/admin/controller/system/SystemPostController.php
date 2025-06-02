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
use app\admin\validate\system\SystemPostValidate;
use app\common\services\system\SystemPostService;
use support\Container;

class SystemPostController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemPostService::class);
        $this->validate = Container::make(SystemPostValidate::class);
    }

}
