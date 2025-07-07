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

use madong\admin\abstract\BaseController;

/**
 * 基类控制器继承的类
 *
 * @author Mr.April
 * @since  1.0
 */
class Base extends BaseController
{

    /**
     * 当前登陆管理员信息
     *
     * @var
     */
    protected $adminInfo;

    /**
     * 当前登陆管理员ID
     *
     * @var string|int
     */
    protected string|int $adminId = 0;

    /**
     * 当前管理员权限
     *
     * @var array
     */
    protected array $auth = [];

    /**
     * 初始化
     */
    protected function initialize(): void
    {
        $request = request();
        if ($request->hasMacro('adminId')) {
            $this->adminId = request()->adminId();//管理员id
        }
        if ($request->hasMacro('adminInfo')) {
            $this->adminInfo = request()->adminInfo();//管理员详情
            $this->auth      = $this->adminInfo['rules'] ?? [];//管理员权限
        }
    }
}
