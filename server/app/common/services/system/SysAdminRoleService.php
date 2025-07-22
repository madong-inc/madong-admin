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

use app\common\dao\system\SysAdminRoleDao;
use core\abstract\BaseService;
use core\casbin\Permission;
use core\enum\system\PolicyPrefix;
use core\exception\handler\AdminException;
use support\Container;

/**
 * @author Mr.April
 * @since  1.0
 */
class SysAdminRoleService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SysAdminRoleDao::class);
    }

    /**
     * 移除用户-关联角色
     *
     * @param array $data
     *
     * @throws \core\exception\handler\AdminException
     */
    public function removeUserRole(array $data)
    {
        try {
            $this->transaction(function () use ($data) {
                $domain = '*';
                foreach ($data as $item) {
                    $this->dao->getModel()->where($item)->delete();
                    Permission::deleteRoleForUser(PolicyPrefix::USER->value . $item['admin_id'], PolicyPrefix::ROLE->value . $item['role_id'], $domain);
                }
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 保存用户-关联角色
     *
     * @param array $data
     *
     * @return void
     */
    public function saveUserRoles(array $data): void
    {
        try {
            $this->transaction(function () use ($data) {
                $domain = '*';
                $this->dao->saveAll($data);
                foreach ($data as $item) {
                    Permission::addRoleForUser(PolicyPrefix::USER->value . $item['admin_id'], PolicyPrefix::ROLE->value . $item['role_id'], $domain);
                }

            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
