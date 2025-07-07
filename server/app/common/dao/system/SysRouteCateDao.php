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

use app\common\model\system\SysRouteCate;
use madong\admin\abstract\BaseDao;

/**
 *
 * 路由分类
 * @author Mr.April
 * @since  1.0
 */
class SysRouteCateDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysRouteCate::class;
    }

}
