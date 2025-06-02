<?php

namespace app\admin\middleware;

use app\common\services\system\SystemAuthService;
use madong\utils\Json;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * Token验证中间件
 *
 * @author Mr.April
 * @since  1.0
 */
class AdminAuthTokenMiddleware implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        $tokenHeader = $request->header(Config('madong.cross.token_name', 'Authorization'));
        $token       = '';
        if ($tokenHeader) {
            $token = trim(ltrim($tokenHeader, 'Bearer'));
        }
        if (!$token) {
            $tokenParam = $request->get('token');
            if ($tokenParam) {
                $token = trim($tokenParam);
            }
        }
        /** @var SystemAuthService $service */
        $service = Container::make(SystemAuthService::class);
        try {
            $adminInfo = $service->parseToken($token);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }

        $request->macro('isAdminLogin', function () use (&$adminInfo) {
            return !is_null($adminInfo);
        });

        $request->macro('adminId', function () use (&$adminInfo) {
            return $adminInfo['id'] ?? '';
        });

        $request->macro('adminInfo', function () use (&$adminInfo) {
            return $adminInfo;
        });

        return $handler($request);
    }
}
