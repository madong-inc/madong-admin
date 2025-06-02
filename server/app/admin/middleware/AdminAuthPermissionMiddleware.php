<?php
namespace app\admin\middleware;

use app\common\services\system\SystemAuthService;
use madong\exception\AuthException;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 权限中间件
 *
 * @author Mr.April
 * @since  1.0
 */
class AdminAuthPermissionMiddleware implements MiddlewareInterface
{

    /**
     * 权限过滤
     *
     * @param \Webman\Http\Request $request
     * @param callable             $handler
     *
     * @return \Webman\Http\Response
     */
    public function process(Request $request, callable $handler): Response
    {
        if (!$request->adminId() || !$request->adminInfo()) {
            throw new AuthException('参数错误');
        }
        //非超级管理员进行权限验证
        if ($request->adminInfo()['is_super'] !== 1) {
            /** @var SystemAuthService $service */
            $service = Container::make(SystemAuthService::class);
            $service->verifyAuth($request);
        }
        return $handler($request);
    }
}
