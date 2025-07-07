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

use app\common\model\platform\TenantSession;
use madong\admin\abstract\BaseDao;

/**
 *
 * 租户会话
 * @author Mr.April
 * @since  1.0
 */
class TenantSessionDao extends BaseDao
{

    protected function setModel(): string
    {
        return TenantSession::class;
    }
}
