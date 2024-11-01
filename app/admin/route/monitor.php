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

use support\Request;
use Webman\Route;

Route::group(function () {
    /**
     * Redis监控
     */
    Route::get('/monitor/redis', [\app\admin\controller\monitor\RedisController::class, 'index'])->name('Redis监控');

    /**
     * 性能监控
     */
    Route::get('/monitor/server', [\app\admin\controller\monitor\ServerController::class, 'index'])->name('性能监控');

})->middleware([
    app\middleware\AllowCrossOriginMiddleware::class,
    app\admin\middleware\AdminAuthTokenMiddleware::class,
    app\admin\middleware\AdminAuthPermissionMiddleware::class,
    app\admin\middleware\AdminLogMiddleware::class,
]);
