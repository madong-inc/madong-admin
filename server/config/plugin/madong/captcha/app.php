<?php

return [
    'enable'  => env('CAPTCHA_ENABLED', false),
    'mode'    => env('CAPTCHA_MODE', 'session'),//session||redis
    'expire'  => 300,//验证码过期时间
    'default' => [
    ],
];



