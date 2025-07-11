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

use app\common\scopes\global\TenantScope;
use app\common\services\platform\TenantSessionService;
use madong\admin\context\TenantContext;
use madong\admin\utils\Json;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 租户识别中间件
 * 该中间件负责在每个请求开始时解析租户信息（如 tenant_id 和 isolation_mode），
 * 设置租户上下文，并在请求结束时清理上下文，确保上下文不会泄漏到其他请求中。
 */
class TenantMiddleware implements MiddlewareInterface
{

    /**
     * 处理请求
     *
     * @param Request  $request
     * @param callable $handler
     *
     * @return Response
     * @throws \Throwable
     */
    public function process(Request $request, callable $handler): Response
    {
        // 1. 如果未开启租户模式，直接跳过中间件
        if (!config('tenant.enabled', false)) {
            return $handler($request);
        }

        /**
         * 免租户验证的 URI 列表（如登录、验证码等）
         */
        $skipUris   = config('tenant.skip_uris', []);
        $currentUri = $request->path();
        if (in_array($currentUri, $skipUris)) {
            return $handler($request);
        }

        // 3.销毁租户上下文
        TenantContext::destroy();

        // 4. 尝试从请求头中获取 clientId（适配大小写）
        $clientId = $request->header('X-Client-ID', null);

        // 5. 尝试从请求头或参数中获取 token
        $tokenHeader = $request->header(config('plugin.madong.jwt.app.jwt.token_name', 'Authorization'));
        $token       = '';
        if ($tokenHeader) {
            // 去掉 'Bearer ' 前缀（如果存在）
            $token = trim(ltrim($tokenHeader, 'Bearer'));
        }

        // 6.如果请求头中没有 token，尝试从 GET 参数中获取
        if (empty($token)) {
            $tokenParam = $request->get('token');
            if ($tokenParam) {
                $token = trim($tokenParam);
            }
        }

        // 7. 优先使用 clientId 解析租户信息，如果 clientId 不存在，则使用 token 解析
        $tenantInfo = null;
        if (!empty($clientId)) {
            //可改用缓存
//            $tenantSessionService = Container::make(TenantSessionService::class);
//            $tenantInfo           = $tenantSessionService->get(['key' => $clientId], null, ['tenant'], '', [TenantScope::class]);
//            $tenantInfo           = $tenantInfo->tenant ?? null; // 替换为实际调用
        }

        // 8.如果 clientId 不存在或无效，尝试通过 token 获取租户信息
        if (empty($tenantInfo) && !empty($token)) {
            //可改用缓存
            $tenantSessionService = Container::make(TenantSessionService::class);
            $tenantSessionInfo    = $tenantSessionService->get(['token' => md5($token)], null, ['tenant'], '', [TenantScope::class]);

            if (!empty($tenantSessionInfo) && !empty($tenantSessionInfo->tenant)) {
                $tenantInfo = $tenantSessionInfo->tenant;
            }
        }

        // 9. 如果仍然无法解析到租户信息，返回未授权响应
        if (empty($tenantInfo) || empty($tenantInfo->id) || empty($tenantInfo->isolation_mode)) {
            return $this->unauthorizedResponse('无法解析租户信息');
        }

        // 10. 提取租户相关信息
        $code          = $tenantInfo->code;
        $tenantId      = $tenantInfo->id;
        $connect       = $tenantInfo->db_name;
        $isolationMode = $tenantInfo->isolation_mode;
        $expired       = $tenantInfo->expired_at ?? null;//过期时间

        // 11. 设置租户上下文
        try {
            TenantContext::setContext($tenantId, $code, $isolationMode, $connect, $expired);
        } catch (\InvalidArgumentException $e) {
            return $this->unauthorizedResponse($e->getMessage());
        }
        return $handler($request);
    }

    /**
     * 返回未授权的响应
     *
     * @param string $message 错误消息
     *
     * @return Response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return Json::fail($message, [], 401);
    }
}
