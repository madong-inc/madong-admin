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

use app\common\dao\platform\TenantSessionDao;
use madong\admin\abstract\BaseService;
use support\Container;

/**
 *
 * 租户会话
 * @author Mr.April
 * @since  1.0
 */
class TenantSessionService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(TenantSessionDao::class);
    }

}
