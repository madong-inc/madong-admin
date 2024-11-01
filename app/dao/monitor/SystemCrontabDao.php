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

namespace app\dao\monitor;

use app\model\monitor\Crontab;
use madong\basic\BaseDao;

class SystemCrontabDao extends BaseDao
{

    protected function setModel(): string
    {
        return Crontab::class;
    }
}
