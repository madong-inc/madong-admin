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

use Webman\Route;
use support\Request;

/**
 * 自动加载crud 目录下的路由
 */
Route::group(function () {
    $path = app_path() . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'route' . DIRECTORY_SEPARATOR . 'crud';

    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require $path . DIRECTORY_SEPARATOR . $file;
            }
        }
    }
});

