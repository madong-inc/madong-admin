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

use app\common\dao\system\SystemCrontabLogDao;
use madong\basic\BaseService;


class SystemCrontabLogService extends BaseService
{
    public function __construct(SystemCrontabLogDao $dao)
    {
        $this->dao = $dao;
    }
}
