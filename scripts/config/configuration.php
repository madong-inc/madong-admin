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
    [
        "group_code"  => "system_storage",
        "code"        => "local",
        "name"        => "本地存储",
        "content"     => json_decode('{"root":"public","dirname":"upload","domain":"http://www.ingenstream.cn"}', true),
        "is_sys"      => 1,
        "enabled"     => 1,
        "create_time" => time(),
        "create_user" => 1,
        "update_time" => time(),
        "update_user" => 1,
        "delete_time" => null,
        "remark"      => null,
    ],
    [
        "group_code"  => "system_storage",
        "code"        => "oss",
        "name"        => "oss",
        "content"     => json_decode('{"accessKeyId":"1","accessKeySecret":"2","bucket":"1","domain":"1","endpoint":"22","dirname":"22"}', true),
        "is_sys"      => 1,
        "enabled"     => 1,
        "create_time" => time(),
        "create_user" => 1,
        "update_time" => time(),
        "update_user" => 1,
        "delete_time" => null,
        "remark"      => null,
    ],
    [
        "group_code"  => "system_storage",
        "code"        => "cos",
        "name"        => "cos",
        "content"     => json_decode('{"secretId":"13","secretKey":"23","bucket":"23","domain":"23","region":"23","dirname":"23"}', true),
        "is_sys"      => 1,
        "enabled"     => 1,
        "create_time" => time(),
        "create_user" => 1,
        "update_time" => time(),
        "update_user" => 1,
        "delete_time" => null,
        "remark"      => null,
    ],
    [
        "group_code"  => "system_sorage",
        "code"        => "qiniu",
        "name"        => "qiniu",
        "content"     => json_decode('{"secretId":"13","secretKey":"23","bucket":"23","domain":"23","region":"23","dirname":"23"}', true),
        "is_sys"      => 1,
        "enabled"     => 1,
        "create_time" => time(),
        "create_user" => 1,
        "update_time" => time(),
        "update_user" => 1,
        "delete_time" => null,
        "remark"      => null,
    ],
    [
        "group_code"  => "system_storage",
        "code"        => "basic",
        "name"        => "文件上传基础配置",
        "content"     => json_decode('{"default":"local","single_limit":"1024","total_limit":"1024","nums":"100","include":"png,image","exclude":"mp4,php"}', true),
        "is_sys"      => 1,
        "enabled"     => 1,
        "create_time" => time(),
        "create_user" => 1,
        "update_time" => time(),
        "update_user" => 1,
        "delete_time" => null,
        "remark"      => null,
    ],
];