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

namespace app\common\enum\system;

use app\common\enum\IEnum;

enum DbType: string implements IEnum
{
    case MYSQL = 'mysql';
    case PGSQL = 'pgsql';
    case SQLITE = 'sqlite';
    case SQL_SERVER = 'sqlsrv';
    case ORACLE = 'oci';

    public function label(): string
    {
        return match($this) {
            self::MYSQL => 'mysql',
            self::PGSQL => 'pgsql',
            self::SQLITE => 'sqlite',
            self::SQL_SERVER => 'sqlsrv',
            self::ORACLE => 'oci',
        };
    }
}
