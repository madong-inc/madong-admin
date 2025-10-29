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

Route::group('/dev', function () {

    /**
     * 定时任务
     */
    Route::group(function () {
        Route::get('/crontab', [\app\admin\controller\dev\CrontabController::class, 'index'])->name('开发平台.定时任务.列表');
        Route::get('/crontab/{id}', [\app\admin\controller\dev\CrontabController::class, 'show'])->name('开发平台.定时任务.详情');
        Route::put('/crontab', [\app\admin\controller\dev\CrontabController::class, 'update'])->name('开发平台.定时任务.更新');
        Route::post('/crontab', [\app\admin\controller\dev\CrontabController::class, 'store'])->name('开发平台.定时任务.保存');
        Route::delete('/crontab/{id}', [\app\admin\controller\dev\CrontabController::class, 'destroy'])->name('开发平台.定时任务.删除');
        Route::put('/crontab/resume', [\app\admin\controller\dev\CrontabController::class, 'resume'])->name('开发平台.定时任务.恢复');
        Route::put('/crontab/pause', [\app\admin\controller\dev\CrontabController::class, 'pause'])->name('开发平台.定时任务.暂停');
        Route::put('/crontab/execute', [\app\admin\controller\dev\CrontabController::class, 'execute'])->name('开发平台.定时任务.执行');

    });

    /**
     * 定时任务日志
     */
    Route::group(function () {
        Route::get('/crontab-log', [\app\admin\controller\dev\CrontabLogController::class, 'index'])->name('开发平台.定时任务.日志列表');
        Route::get('/crontab-log/{id}', [\app\admin\controller\dev\CrontabLogController::class, 'show'])->name('开发平台.定时任务.日志详情');
        Route::delete('/crontab-log/{id}', [\app\admin\controller\dev\CrontabLogController::class, 'destroy'])->name('开发平台.定时任务.日志删除');
    });

    /**
     * 网关管理
     */
    Route::group('/gateway', function () {

        /**
         * 限制规则
         */
        Route::group(function () {
            Route::get('/limiter', [\app\admin\controller\dev\RateLimiterController::class, 'index'])->name('开发平台.网关管理.限制规则.列表');
            Route::get('/limiter/{id}', [\app\admin\controller\dev\RateLimiterController::class, 'show'])->name('开发平台.网关管理.限制规则.详情');
            Route::put('/limiter', [\app\admin\controller\dev\RateLimiterController::class, 'update'])->name('开发平台.网关管理.限制规则.更新');
            Route::put('/limiter/change-status', [\app\admin\controller\dev\RateLimiterController::class, 'changeStatus'])->name('开发平台.网关管理.限制规则.更新状态');
            Route::post('/limiter', [\app\admin\controller\dev\RateLimiterController::class, 'store'])->name('开发平台.网关管理.限制规则.保存');
            Route::delete('/limiter/{id}', [\app\admin\controller\dev\RateLimiterController::class, 'destroy'])->name('开发平台.网关管理.限制规则.删除');
        });

        /**
         * 限制名单
         */
        Route::group(function () {
            Route::get('/blacklist', [\app\admin\controller\dev\RateRestrictionsController::class, 'index'])->name('开发平台.网关管理.限访名单.列表');
            Route::get('/blacklist/{id}', [\app\admin\controller\dev\RateRestrictionsController::class, 'show'])->name('开发平台.网关管理.限访名单.详情');
            Route::put('/blacklist', [\app\admin\controller\dev\RateRestrictionsController::class, 'update'])->name('开发平台.网关管理.限访名单.更新');
            Route::put('/blacklist/change-status', [\app\admin\controller\dev\RateRestrictionsController::class, 'changeStatus'])->name('开发平台.网关管理.限访名单.更新状态');
            Route::post('/blacklist', [\app\admin\controller\dev\RateRestrictionsController::class, 'store'])->name('开发平台.网关管理.限访名单.保存');
            Route::delete('/blacklist/{id}', [\app\admin\controller\dev\RateRestrictionsController::class, 'destroy'])->name('开发平台.网关管理.限访名单.删除');
        });

    });

});
