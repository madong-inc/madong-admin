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

use app\common\model\system\SysDictItem;
use core\abstract\BaseDao;

/**
 * 字典项
 *
 * @author Mr.April
 * @since  1.0
 */
class SysDictItemDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysDictItem::class;
    }

}
