<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

return [
    "paths" => [
        "migrations" => "resource/database/migrations",
        "seeds"      => "resource/database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "mysql",
            "host"    => "43.138.153.216",
            "name"    => "md_admin",
            "user"    => "md_admin",
            "pass"    => "Mk38f7mh8mia6ZL4",
            "port"    => "3306",
            "charset" => "utf8"
        ]
    ]
];
