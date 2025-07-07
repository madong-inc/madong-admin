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
use app\common\services\platform\TenantService;
use madong\admin\context\TenantContext;
use madong\exception\handler\ServerErrorHttpException;
use madong\exception\handler\UnauthorizedHttpException;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 域名切换数据源
 *
 * @author Mr.April
 * @since  1.0
 */
class MultiSiteMiddleware implements MiddlewareInterface
{

    /**
     * @throws \madong\exception\handler\UnauthorizedHttpException
     * @throws \madong\exception\handler\ServerErrorHttpException
     */
    public function process(Request $request, callable $handler): Response
    {
        $domain = $request->header()['X-Origin-Domain'] ?? 'default';
        TenantContext::destroy();
        $tenantService = Container::make(TenantService::class);
        $tenantInfo    = $tenantService->get(['domain' => $domain], null, [], [TenantScope::class]);
        if (empty($tenantInfo)) {
            throw new ServerErrorHttpException();
        }

        $code          = $tenantInfo->code;
        $tenantId      = $tenantInfo->id;
        $connect       = $tenantInfo->db_name;
        $isolationMode = $tenantInfo->isolation_mode;

        try {
            TenantContext::setContext($tenantId, $code, $isolationMode, $connect);
        } catch (\InvalidArgumentException $e) {
            throw new UnauthorizedHttpException();
        }

        return $handler($request);
    }
}