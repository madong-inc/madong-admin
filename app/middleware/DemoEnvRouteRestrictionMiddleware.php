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

use app\adminapi\middleware\helper\SseHelper;
use core\jwt\JwtToken;
use core\tool\Json;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 演示限制操作-实际生产移除
 *
 * @author Mr.April
 * @since  1.0
 */
class DemoEnvRouteRestrictionMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 不允许操作的路由
        $restrictedRoutes = [
            '/adminapi/system/user',
            '/adminapi/system/user/\d+',
            '/adminapi/system/menu',
            '/adminapi/system/menu/\d+',
            '/adminapi/system/role',
            '/adminapi/system/role/\d+',
            '/adminapi/system/dept/',
            '/adminapi/system/dept/\d+',
            '/adminapi/system/post',
            '/adminapi/system/post/\d+',
            '/adminapi/system/dict',
            '/adminapi/system/dict/\d+',
            '/adminapi/system/dict-item',
            '/adminapi/system/dict-item/\d+',
            '/adminapi/system/recycle-bin',
            '/adminapi/system/recycle-bin/\d+',
            '/adminapi/system/config',
            '/adminapi/dev/crontab',
            '/adminapi/dev/crontab/\d+',
            '/adminapi/platform/db',
            '/adminapi/platform/tenant-subscription',
            '/adminapi/platform/tenant-subscription/\d+',
            '/adminapi/platform/tenant-member',
            '/adminapi/platform/tenant-member/\d+',
            '/adminapi/generator/\d+/deploy',
            //offline插件限制路由
            '/adminapi/market/category',
            '/adminapi/market/category/\d+',
            '/adminapi/market/apps',
            '/adminapi/market/apps/\d+',
            '/adminapi/market/apps/publish-version',
            '/adminapi/docs',
            '/adminapi/docs/\d+',
            '/adminapi/doc/content',
            '/adminapi/doc/content/\d+',
            '/adminapi/ask/answer',
            '/adminapi/ask/answer/\d+',
            '/adminapi/ask/category',
            '/adminapi/ask/category/\d+',
            '/adminapi/ask/question',
            '/adminapi/ask/question/\d+',
            '/adminapi/ask/tag',
            '/adminapi/ask/tag/\d+',
            '/adminapi/generator/code/\d+/deploy',
            //插件服务
            '/adminapi/plugin/install',
            '/adminapi/plugin',
            '/adminapi/plugin/uninstall',
            //WEB终端服务
            '/adminapi/terminal',
            '/adminapi/terminal/\d+',
        ];
        $currentPath      = $request->path();

        $method = $request->method();
        
        // 检查当前路径是否在限制的路由中
        foreach ($restrictedRoutes as $pattern) {
            if (preg_match("#^$pattern$#", $currentPath)) {
                // 检查是否为 root 用户
                if ($this->isRootUser()) {
                    return $handler($request);
                }

                // 检查是否为SSE请求
                $isSseRequest = SseHelper::isSseRequest($request);

                // SSE请求需要单独处理
                if ($isSseRequest) {
                    return SseHelper::sendSseErrorViaConnection($request, '演示环境,不支持当前操作');
                }

                // 非SSE的写操作（PUT, POST, DELETE）也限制
                if (in_array(strtoupper($method), ['PUT', 'POST', 'DELETE'])) {
                    return Json::fail('演示环境,不支持当前操作');
                }

                // 对于GET请求，如果不是SSE，默认允许（演示环境可能允许查看）
            }
        }

    // 继续处理请求
    return $handler($request);
}

    /**
     * 检查是否为 root 用户
     */
    private function isRootUser(): bool
    {
        $payload = (new JwtToken())->getPayloadFromRequest();
        $extra    = is_array($payload) ? ($payload['extra'] ?? null) : (is_object($payload) ? ($payload->extra ?? null) : null);

        if (is_object($extra)) {
            return ($extra->user_name ?? '') === 'root';
        }

        if (is_array($extra)) {
            return ($extra['user_name'] ?? '') === 'root';
        }

        return false;
    }

}
