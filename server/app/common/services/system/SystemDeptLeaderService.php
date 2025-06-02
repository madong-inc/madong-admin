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

namespace app\common\services\system;

use app\common\dao\system\SystemDeptLeaderDao;
use madong\basic\BaseService;
use support\Container;

/**
 * @author Mr.April
 * @since  1.0
 */
class SystemDeptLeaderService extends BaseService
{

    public function __construct(SystemDeptLeaderDao $dao)
    {
        $this->dao = $dao;
    }

}
