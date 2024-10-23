<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AllowCrossOriginMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $clientIP = $request->getRemoteIp();
        // ip不在允许范围内 返回403
//        if (!$this->isAllowedIP($clientIP)) {
//            if ($request->method() === 'OPTIONS') {
//                return $this->handlePreflightRequest();
//            }
//            $response = new Response(403);
//            $this->addCorsHeaders($response);
//            return $response;
//        }
        // 检查是否为预检请求
        if ($request->method() === 'OPTIONS') {
            return $this->handlePreflightRequest();
        }
        // 处理实际请求
        $response = $handler($request);
        // 添加 CORS 头部信息
        $this->addCorsHeaders($response);
        return $response;
    }

    protected function handlePreflightRequest(): Response
    {
        $response = new Response();
        $this->addCorsHeaders($response);
        $response->header('Access-Control-Max-Age', '3600');
        return $response;
    }

    protected function addCorsHeaders(Response $response): void
    {
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization,Authori-zation, Author');
        $response->header('Access-Control-Allow-Credentials', 'true');
    }

    protected function isAllowedIP($ip): bool
    {
        $domain = config('app.domain', []);
        return in_array($ip, $domain);
    }
}
