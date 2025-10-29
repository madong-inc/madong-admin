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
    'enable'                => true,
    'enum_scan_directories' => [
        base_path('core/enum/system'),//目录枚举
        base_path('core/enum/platform'),//目录枚举
    ],
];
