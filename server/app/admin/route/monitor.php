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

Route::group(function () {
    /**
     * Redis监控
     */
    Route::get('/monitor/redis', [\app\admin\controller\monitor\RedisController::class, 'index'])->name('系统监控.Redis.详情');

    /**
     * 性能监控
     */
    Route::get('/monitor/server', [\app\admin\controller\monitor\ServerController::class, 'index'])->name('系统监控.性能.监控');

});
