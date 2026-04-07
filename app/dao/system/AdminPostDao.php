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

namespace app\dao\system;


use app\model\system\AdminPost;
use core\base\BaseDao;

class AdminPostDao extends BaseDao
{

    protected function setModel(): string
    {
        return AdminPost::class;
    }
}
