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

return [
    'captcha'           => [
        'mode'    => env('CAPTCHA_MODE','session'),//session||redis
        'expire'  => 300,//验证码过期时间
        'default' => [

        ],
    ],
    'captcha_open_flag' => env('CAPTCHA_ENABLED',false),//登录验证码状态
];

