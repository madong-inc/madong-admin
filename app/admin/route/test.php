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

Route::group('/test', function () {
    Route::get('/index', [\app\admin\controller\TestController::class, 'index'])->name(' 安装首页');
});
