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

namespace app\common\dao\platform;

use app\common\model\platform\TenantSubscriptionCasbin;
use core\abstract\BaseDao;

/**
 * 套餐-关联策略表
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSubscriptionPlanDao extends BaseDao
{

    protected function setModel(): string
    {
        return TenantSubscriptionCasbin::class;
    }
}
