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

use Webman\Route;

Route::group('/install', function () {
    Route::get('/index', [\app\admin\controller\InstallController::class, 'index'])->name(' 安装首页');
    Route::post('/step1', [\app\admin\controller\InstallController::class, 'step1'])->name('创建数据库');
    Route::post('/step2', [\app\admin\controller\InstallController::class, 'step2'])->name('添加管理员');

});
