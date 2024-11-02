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

namespace app\dao\system;


use app\model\system\SystemCrontabLog;
use madong\basic\BaseDao;

class SystemCrontabLogDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemCrontabLog::class;
    }
}
