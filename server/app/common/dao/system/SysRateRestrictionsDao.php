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

use app\common\model\system\SysRateRestrictions;
use madong\admin\abstract\BaseDao;

/**
 *
 * 限访名单
 * @author Mr.April
 * @since  1.0
 */
class SysRateRestrictionsDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysRateRestrictions::class;
    }
}
