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

namespace app\services\system;

use app\dao\system\SystemDeptLeaderDao;
use madong\basic\BaseService;
use support\Container;

/**
 *
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemDeptLeaderService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemDeptLeaderDao::class);
    }

}
