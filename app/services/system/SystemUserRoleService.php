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

namespace app\services\system;

use app\dao\system\SystemUserRoleDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;


/**
 *
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemUserRoleService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUserRoleDao::class);
    }

    /**
     * 移除用户-关联角色
     *
     * @param array $data
     */
    public function removeUserRole(array $data)
    {
        try {
            $this->transaction(function () use ($data) {
                foreach ($data as $item) {
                    $this->dao->delete($item);
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
     * @return bool
     */
    public function saveUserRoles(array $data):bool
    {
        try {
            return $this->transaction(function () use ($data) {
                return $this->dao->saveAll($data);
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
