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

namespace app\dao\gateway;

use app\model\gateway\RateRestrictions;
use core\base\BaseDao;

/**
 *
 * 限访名单
 * @author Mr.April
 * @since  1.0
 */
class RateRestrictionsDao extends BaseDao
{

    protected function setModel(): string
    {
        return RateRestrictions::class;
    }
}
