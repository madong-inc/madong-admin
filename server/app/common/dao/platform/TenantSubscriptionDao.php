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


use app\common\model\platform\TenantSubscription;
use madong\admin\abstract\BaseDao;

/**
 * 套餐订阅套餐
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSubscriptionDao extends BaseDao
{

    protected function setModel(): string
    {
        return TenantSubscription::class;
    }
}
