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

    /**
     * 定时任务
     */
    Route::group(function () {
        Route::get('/monitor/crontab', [\app\admin\controller\monitor\CrontabController::class, 'index'])->name('系统监控.定时任务.列表');
        Route::get('/monitor/crontab/{id}', [\app\admin\controller\monitor\CrontabController::class, 'show'])->name('系统监控.定时任务.详情');
        Route::put('/monitor/crontab', [\app\admin\controller\monitor\CrontabController::class, 'update'])->name('系统监控.定时任务.更新');
        Route::post('/monitor/crontab', [\app\admin\controller\monitor\CrontabController::class, 'store'])->name('系统监控.定时任务.保存');
        Route::delete('/monitor/crontab/{id}', [\app\admin\controller\monitor\CrontabController::class, 'destroy'])->name('系统监控.定时任务.删除');

        Route::put('/monitor/crontab/resume', [\app\admin\controller\monitor\CrontabController::class, 'resume'])->name('系统监控.定时任务.恢复');
        Route::put('/monitor/crontab/pause', [\app\admin\controller\monitor\CrontabController::class, 'pause'])->name('系统监控.定时任务.暂停');
        Route::put('/monitor/crontab/execute', [\app\admin\controller\monitor\CrontabController::class, 'execute'])->name('系统监控.定时任务.执行');

    });

    /**
     * 定时任务日志
     */
    Route::group(function () {
        Route::get('/monitor/crontab-log', [\app\admin\controller\monitor\CrontabLogController::class, 'index'])->name('系统监控.定时任务.日志列表');
        Route::get('/monitor/crontab-log/{id}', [\app\admin\controller\monitor\CrontabLogController::class, 'show'])->name('系统监控.定时任务.日志详情');
        Route::delete('/monitor/crontab-log/{id}', [\app\admin\controller\monitor\CrontabLogController::class, 'destroy'])->name('系统监控.定时任务.日志删除');
    });

})->middleware([
    \app\common\middleware\AllowCrossOriginMiddleware::class,
    app\admin\middleware\AdminAuthTokenMiddleware::class,
    app\admin\middleware\AdminAuthPermissionMiddleware::class,
    app\admin\middleware\AdminLogMiddleware::class,
]);
