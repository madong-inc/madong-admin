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

namespace app\common\services\system;

use app\common\dao\system\SysCrontabLogDao;
use core\abstract\BaseService;

class SysCrontabLogService extends BaseService
{
    public function __construct(SysCrontabLogDao $dao)
    {
        $this->dao = $dao;
    }
}
