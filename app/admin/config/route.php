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
 * 动态加载route目录下的路由文件
 */
Route::group(function () {
    $path = app_path() . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'route';
    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require $path . DIRECTORY_SEPARATOR . $file;
            }
        }
    }
});
