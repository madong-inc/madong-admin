<?php
declare(strict_types=1);
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

namespace app\service\admin\crontab;

use app\dao\crontab\CrontabLogDao;
use core\base\BaseService;

class CrontabLogService extends BaseService
{
    public function __construct(CrontabLogDao $dao)
    {
        $this->dao = $dao;
    }
}
