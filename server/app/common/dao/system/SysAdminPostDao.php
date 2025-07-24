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

namespace app\common\dao\system;


use app\common\model\system\SysAdminPost;
use core\abstract\BaseDao;

class SysAdminPostDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysAdminPost::class;
    }
}
