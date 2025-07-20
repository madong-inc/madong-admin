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

use app\common\model\system\SysRateLimiter;
use core\abstract\BaseDao;

/**
 *  限流
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRateLimiterDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysRateLimiter::class;
    }
}
