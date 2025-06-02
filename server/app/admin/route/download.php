<?php

/**
 * 导出文件下载
 */

use support\Request;
use Webman\Route;


Route::group('/export', function () {
    Route::get('/download', [\app\admin\controller\system\SystemUploadController::class, 'downloadExcel'])->name('导出文件');
});
