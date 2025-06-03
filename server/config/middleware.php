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

return [
    'admin' => [
        \app\admin\middleware\RouteRestrictionMiddleware::class,// 演示过滤中间件
    ],
    // 全局中间件
    '' => [
        app\common\middleware\Lang::class,//多语言切换中间件
    ]
];
