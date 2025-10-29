<?php

return [
    'enable'            =>  env('CAPTCHA_ENABLED', false),
    'captcha'           => [
        'mode'    => 'session',//session||redis
        'expire'  => 300,//验证码过期时间
        'default' => [
        ],
    ],
];

