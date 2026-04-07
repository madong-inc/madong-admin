<?php
declare(strict_types=1);
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

namespace app\dao\notice;

use app\model\notice\Notice;
use core\base\BaseDao;

class NoticeDao extends BaseDao
{

    protected function setModel(): string
    {
        return Notice::class;
    }
}
