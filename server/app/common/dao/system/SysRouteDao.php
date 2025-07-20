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

use app\common\model\system\SysRoute;
use core\abstract\BaseDao;

/**
 * 路由
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRouteDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysRoute::class;
    }
}
