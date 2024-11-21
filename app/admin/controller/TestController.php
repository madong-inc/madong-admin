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

namespace app\admin\controller;

use madong\adapter\TestAbd;
use support\Container;
use support\Request;

class TestController
{
    public function index(Request $request)
    {
        $dao    = Container::make(TestAbd::class);
        $result = $dao->tex();

        var_dump($result);

    }
}
