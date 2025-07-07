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

use app\common\model\system\SysRoleScopeDept;
use madong\admin\abstract\BaseDao;

/**
 *
 * 角色数据权限
 * @author Mr.April
 * @since  1.0
 */
class SysRoleScopeDeptDao extends BaseDao
{
    protected function setModel(): string
    {
        return SysRoleScopeDept::class;
    }
}
