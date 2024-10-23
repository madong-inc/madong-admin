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

use app\model\system\SystemDeptLeader;
use madong\basic\BaseDao;

class SystemDeptLeaderDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemDeptLeader::class;
    }
}
