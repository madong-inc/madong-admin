<?php

return [
    // 跨域配置
    'cross'       => [
        // token信息
        'token_name'   => 'Authorization',
        // 过期时间 (小时)
        'token_expire' => 6,
    ],
    // 中间件白名单
    'white_list'  => [
        '/core/captcha',
        '/core/login',
    ],
    // 是否开启后端接口权限认证
    'server_auth' => true,
    // 缓存配置
    'cache'       => [
        'namespace' => 'mesh',
        'type'      => 'redis',//redis||file
        'prefix'    => '',
    ],
    // 验证码存储模式
    'captcha'     => [
        // 验证码存储模式 session或者redis
        'mode'   => 'session',
        // 验证码过期时间 (秒)
        'expire' => 300,
    ],
];
