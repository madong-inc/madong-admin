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

use app\common\enum\system\PolicyPrefix;
use app\common\services\system\SysMenuService;
use Casbin\Exceptions\CasbinException;
use madong\admin\context\TenantContext;
use madong\casbin\Permission;
use madong\exception\handler\ForbiddenHttpException;
use madong\exception\handler\UnauthorizedHttpException;
use madong\jwt\JwtToken;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 权限处理-  1.跨域处理  2.JWT验证   3.租户验证   4.权限验证 【一定要注意权限验证放到租户后面要】
 *
 * @author Mr.April
 * @since  1.0
 */
class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @throws \madong\exception\handler\UnauthorizedHttpException
     * @throws \madong\exception\handler\ForbiddenHttpException
     * @throws \Exception
     */
    public function process(Request $request, callable $handler): Response
    {

        $rule   = $request->path();
        $method = $request->method();

        // 不受控接口列表
        $uncontrolledRoutes = [
            '/system/auth/user-info',                       //用户信息
            '/system/logout',                               //用户退出
            '/system/auth/user-menus',                      //用户菜单
            '/system/auth/perm-code',                       //用户权限codes
            '/system/dict/get-by-dict-type',                //数据字典
            '/system/message/notify-on-first-login-to-all', //消息广播
            '/system/auth/refresh',                         //刷新Token
            '/platform/account-sets',                       //租户获取
            '/system/get-captcha-open-flag',                //是否启用验证码
            '/system/captcha',                              //验证码获取
            '/system/login',                                //登录
        ];

        // 直接跳过不受控接口
        if (in_array($rule, $uncontrolledRoutes)) {
            return $handler($request);
        }

        $userId = JwtToken::getCurrentId();
        if ($userId === 0) {
            throw new UnauthorizedHttpException();
        }
        $userData = JwtToken::getExtend();
        // 顶级管理员直接跳过权限验证
        if ($userData['is_super'] == 1) {
            return $handler($request);
        }

        // 获取权限服务
        $service = new SysMenuService();
        $authAll = $service->getAllAuth();

        // 检查请求方法是否在授权列表中
        if (!isset($authAll[strtolower($method)]) || !in_array($rule, $authAll[strtolower($method)])) {
            return $handler($request);
        }

        $tenantId = TenantContext::getTenantCode();
        $uid      = PolicyPrefix::USER->value . $userId;
        $domain   = PolicyPrefix::DOMAIN->value . $tenantId;
        $policy   = PolicyPrefix::ROUTE->value . $rule;

        // 权限检查
        try {
            if (!Permission::enforce($uid, $domain, $policy, '*', $method, '*')) {
                throw new ForbiddenHttpException();
            }
        } catch (CasbinException $exception) {
            throw new ForbiddenHttpException($exception->getMessage());
        }

        return $handler($request);
    }

}
