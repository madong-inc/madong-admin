<?php

return [
    'enable'  => true,
    // 异常配置
    'handler' => [
        // 不需要记录错误日志
        'dont_report' => [
            madong\exception\handler\AccessDeniedHttpException::class,
            madong\exception\handler\BadRequestHttpException::class,
            madong\exception\handler\ForbiddenHttpException::class,
            madong\exception\handler\NotFoundHttpException::class,
            madong\exception\handler\ServerErrorHttpException::class,
            madong\exception\handler\TooManyRequestsHttpException::class,
            madong\exception\handler\UnauthorizedHttpException::class,
        ],
        // 自定义HTTP状态码
        'status'      => [
            'validate'                  => 400, // 验证器异常
            'jwt_token'                 => 401, // 认证失败
            'jwt_token_expired'         => 401, // 访问令牌过期
            'jwt_refresh_token_expired' => 402, // 刷新令牌过期
            'server_error'              => 500, // 服务器内部错误
        ],
        // 自定义响应消息
        'body'        => [
            'code' => 0,
            'msg'  => '服务器内部异常',
            'data' => null,
        ],
        /** 异常报警域名标题 */
        'domain'      => [
            'dev'  => 'dev-api.madong.tech', // 开发环境
            'test' => 'test-api.madong.tech', // 测试环境
            'pre'  => 'pre-api.madong.tech', // 预发环境
            'prod' => 'api.madong.tech',  // 生产环境
        ],
    ],

];
