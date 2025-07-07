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
    'enabled'                  => env('APP_TENANT_ENABLED', false),//租户开关
    'auto_select_first_tenant' => env('APP_TENANT_AUTO_SELECT_FIRST', true),//是否自动选择租户模式
    'skip_uris'                => [
        '/system/captcha',
        '/platform/account-sets',
        '/system/get-captcha-open-flag',
        '/system/login',
        '/test',
    ]
];
