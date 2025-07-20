<?php

return [
    'enable'  => true,
    'captcha' => [
        'mode'    => 'session',//session||redis
        'expire'  => 300,//验证码过期时间
        'default' => [
        ],
    ],
];

