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

use app\model\system\SystemCrontab;
use app\services\system\SystemCrontabLogService;
use madong\basic\BaseDao;
use think\Container;

class SystemCrontabDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemCrontab::class;
    }

}
