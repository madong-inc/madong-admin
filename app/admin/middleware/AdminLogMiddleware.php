<?php

namespace app\admin\middleware;


use Webman\Event\Event;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 *
 * 日志中间件
 * @author Mr.April
 * @since  1.0
 */
class AdminLogMiddleware implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        $response = $handler($request);
        Event::emit('user.action', true);
        return $response;
    }
}
