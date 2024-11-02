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

    /**
     * 定时任务
     */
    Route::group(function () {
        Route::get('/monitor/crontab', [\app\admin\controller\monitor\CrontabController::class, 'index'])->name('列表');
        Route::get('/monitor/crontab/{id}', [\app\admin\controller\monitor\CrontabController::class, 'show'])->name('详情');
        Route::put('/monitor/crontab/{id}', [\app\admin\controller\monitor\CrontabController::class, 'update'])->name('更新');
        Route::post('/monitor/crontab', [\app\admin\controller\monitor\CrontabController::class, 'store'])->name('保存');
        Route::delete('/monitor/crontab/{id}', [\app\admin\controller\monitor\CrontabController::class, 'destroy'])->name('删除');

        Route::post('/monitor/crontab/start', [\app\admin\controller\monitor\CrontabController::class, 'start'])->name('启动');
        Route::post('/monitor/crontab/resume', [\app\admin\controller\monitor\CrontabController::class, 'resume'])->name('恢复');
        Route::post('/monitor/crontab/pause', [\app\admin\controller\monitor\CrontabController::class, 'pause'])->name('暂停');
        Route::post('/monitor/crontab/execute', [\app\admin\controller\monitor\CrontabController::class, 'execute'])->name('立即执行');

    });

})->middleware([
    app\middleware\AllowCrossOriginMiddleware::class,
    app\admin\middleware\AdminAuthTokenMiddleware::class,
    app\admin\middleware\AdminAuthPermissionMiddleware::class,
    app\admin\middleware\AdminLogMiddleware::class,
]);
