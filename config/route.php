<?php
/**
 * This file is part of webman.
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

/**
 * 引入admin模块路由
 */
require_once app_path('admin/config/route.php');


Route::get('/', function () {
    // 1. 检查是否已安装（通过 `install.lock` 文件）
    $lockFile = base_path('install.lock');
    if (!file_exists($lockFile)) {
        // 重定向到安装页面
        return redirect('/app/install/index');
    }

    //2.是否有安装前端页面
    $path = base_path('public/index.html');
    if (file_exists($path)) {
        $content = file_get_contents($path);
        return response($content);
    }

    //2.没有前端页面显示readme.md
    $path = base_path('README.md');
    if (file_exists($path)) {
        $content     = file_get_contents($path);
        $parsedown   = new Parsedown();
        $htmlContent = $parsedown->text($content);
        $htmlContent = preg_replace('/<a href="([^"]+)"/', '<a href="$1" target="_blank" rel="noopener noreferrer"', $htmlContent);

        return response("
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>madong-admin快速开发框架</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 0;
                        margin: 0;
                        overflow: hidden;
                    }
                    .markdown-body {
                        padding: 20px;
                        background-color: #f6f8fa;
                        max-height: 100vh;
                        overflow-y: hidden;
                        position: relative;
                    }
                    .markdown-body:hover {
                        overflow-y: auto;
                    }
                    /* 滚动条样式 */
                    .markdown-body::-webkit-scrollbar {
                        width: 8px;
                    }
                    .markdown-body::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 4px;
                    }
                    .markdown-body::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }
                    .markdown-body::-webkit-scrollbar-track {
                        background: transparent;
                    }
                </style>
            </head>
            <body>
                <div class='markdown-body'>
                    $htmlContent
                </div>
            </body>
            </html>
        ")->withHeaders(['Content-Type' => 'text/html']);
    }

    //3.readme.md也没有输出异常404
    return response('File not found', 404);

});

/**
 * 关闭默认路由
 */
Route::disableDefaultRoute();



