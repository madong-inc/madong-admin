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

use app\middleware\RateLimiterMiddleware;

return [
    //应用中间件
    'adminapi' => [
        RateLimiterMiddleware::class,//限流中间件
    ],
    // 超全局中间件-覆盖插件
    '@'        => [
        \app\middleware\AllowCrossOriginMiddleware::class,//跨域中间件
        \app\middleware\Lang::class,//多语言切换中间件
//        \app\middleware\DemoEnvRouteRestrictionMiddleware::class,// 演示过滤中间件
    ],
    // 全局中间件-主项目有效
    ''         => [
    ],
];
