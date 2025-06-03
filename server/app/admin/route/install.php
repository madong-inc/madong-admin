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

/**
 * 后端安装
 */
Route::group('/install', function () {
    Route::get('/index', [\app\admin\controller\InstallController::class, 'index'])->name('安装.首页');
    Route::post('/step1', [\app\admin\controller\InstallController::class, 'step1'])->name('安装.步骤.第一步');
    Route::post('/step2', [\app\admin\controller\InstallController::class, 'step2'])->name('安装.步骤.第二步');
});
