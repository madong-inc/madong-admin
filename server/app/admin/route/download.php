<?php

/**
 * 导出文件下载
 */

use support\Request;
use Webman\Route;

/**
 * 文件下载
 */
Route::group('/export', function () {
    Route::get('/download', [\app\admin\controller\system\SystemUploadController::class, 'downloadExcel'])->name('文件下载.导出Excel');
});
