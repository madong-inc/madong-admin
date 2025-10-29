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

namespace app\middleware;

use core\jwt\JwtToken;
use core\exception\handler\UnauthorizedHttpException;
use core\utils\Json;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * JwtToken验证
 *
 * @author Mr.April
 * @since  1.0
 */
class AuthTokenMiddleware implements MiddlewareInterface
{

    /**
     * @throws \core\exception\handler\UnauthorizedHttpException
     */
    public function process(Request $request, callable $handler): Response
    {

        $rule   = $request->path();
        $action = request()->method();

        // 不需要Token验证的接口
        $uncontrolledRoutes = [
            '/system/login',                                //用户登录
            '/system/logout',                               //用户退出
            '/system/auth/refresh',                         //刷新Token
            '/platform/account-sets',                       //租户获取
            '/system/get-captcha-open-flag',
            '/system/captcha',
            '/system/auth/public-key',                       //公钥获取
            '/system/config/info',                           //系统配置
        ];
        if (in_array($rule, $uncontrolledRoutes)) {
            return $handler($request);
        }

        try {
            $userId = JwtToken::getCurrentId();
            if (0 === $userId) {
                throw new UnauthorizedHttpException();
            }
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], 401);
        }
        return $handler($request);
    }
}
