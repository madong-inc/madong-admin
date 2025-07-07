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

namespace app\common\services\system;

use app\common\dao\system\SysAdminTenantRoleDao;
use madong\admin\abstract\BaseService;
use support\Container;

/**
 * 管理员-租户角色
 *
 * @author Mr.April
 * @since  1.0
 */
class SysAdminTenantRoleService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SysAdminTenantRoleDao::class);
    }
}
