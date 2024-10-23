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


Route::group('/test', function () {
   Route::any('/res', function (Request $request) {return response('res');});

});
