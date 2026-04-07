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

namespace app\dao\org;

use app\model\org\DeptLeader;
use core\base\BaseDao;

class DeptLeaderDao extends BaseDao
{

    protected function setModel(): string
    {
        return DeptLeader::class;
    }
}
