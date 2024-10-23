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

use app\model\system\SystemPost;
use madong\basic\BaseDao;

class SystemPostDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemPost::class;
    }
}
