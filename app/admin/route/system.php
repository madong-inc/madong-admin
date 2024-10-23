<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */

use support\Request;
use Webman\Route;

/*
 * 无需授权的接口
 */
Route::group(function () {
    Route::post('/system/login', [\app\admin\controller\LoginController::class, 'login'])->name('登录');
    Route::post('/system/logout', [\app\admin\controller\LoginController::class, 'logout'])->name('注销');
    Route::get('/auth/captcha', [\app\admin\controller\LoginController::class, 'captcha'])->name('验证码');
});

