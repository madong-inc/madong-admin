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

namespace app\dao\system;

use app\model\system\SystemOperateLog;
use madong\basic\BaseDao;

/**
 *
 * 行为日志Dao
 * @author Mr.April
 * @since  1.0
 */
class SystemOperateLogDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemOperateLog::class;
    }
}
