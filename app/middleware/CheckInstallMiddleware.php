<?php
declare(strict_types=1);
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 安装检查中间件
 *
 * @author Mr.April
 * @since  1.0
 */
class CheckInstallMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 检查是否已安装
        $lockFileExists = file_exists(base_path() . '/install.lock');
        if (!$lockFileExists) {
            // 如果未安装，重定向到安装页面
            return redirect('/install');
        }
        // 如果已安装，继续处理请求
        return $handler($request);
    }
}