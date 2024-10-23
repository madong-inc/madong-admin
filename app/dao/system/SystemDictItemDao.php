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

use app\model\system\SystemDictItem;
use madong\basic\BaseDao;

class SystemDictItemDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemDictItem::class;
    }

}
