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


/**
 * 后端首页
 */
Route::get('/', function() {
    $path = base_path('README.md');

    if (file_exists($path)) {
        $content = file_get_contents($path);
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($content);
        $htmlContent = preg_replace('/<a href="([^"]+)"/', '<a href="$1" target="_blank" rel="noopener noreferrer"', $htmlContent);

        return response("
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>MaDong Admin管理系统</title>
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

    return response('File not found', 404);
});

/**
 * 关闭默认路由
 */
Route::disableDefaultRoute();


