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

namespace app\common\services\platform;



use app\common\dao\platform\TenantSubscriptionPlanDao;
use core\abstract\BaseService;
use support\Container;

/**
 *
 * 租户套餐
 * @author Mr.April
 * @since  1.0
 */
class TenantSubscriptionPlanService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(TenantSubscriptionPlanDao::class);
    }



}
