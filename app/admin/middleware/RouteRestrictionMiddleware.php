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

namespace app\admin\middleware;

use madong\utils\Json;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class RouteRestrictionMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 不允许操作的路由
        $restrictedRoutes = [
            '/system/user',
            '/system/user/\d+',
            '/system/menu',
            '/system/menu/\d+',
            '/system/role',
            '/system/role/\d+',
            '/system/dept/',
            'system/dept/\d+',
            '/system/dict',
            '/system/dict/\d+',
            '/system/dict-item',
            '/system/dict-item/\d+',
            '/system/recycle-bin',
            '/system/recycle-bin/\d+',
            '/system/config',
            '/monitor/crontab',
            '/monitor/crontab/\d+',
        ];
        $currentPath      = $request->path();

        $method = $request->method();
        // 检查当前路径是否在限制的路由中
        if (in_array($method, ['PUT', 'POST', 'DELETE'])) {
            foreach ($restrictedRoutes as $pattern) {
                if (preg_match("#^$pattern$#", $currentPath)) {
//                    return Json::fail('演示环境,不支持当前操作');
                }
            }
        }

        // 继续处理请求
        return $handler($request);
    }
}
