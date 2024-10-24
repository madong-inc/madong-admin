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

use app\dao\system\SystemDeptDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;
use think\facade\Db;

/**
 * @method save(array $data)
 */
class SystemDeptService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemDeptDao::class);
    }

    /**
     * 删除部门
     *
     * @param array|string $data
     */
//    public function batchDelete(array|string $data): void
//    {
//        try {
//            if (is_string($data)) {
//                $data = array_map('trim', explode(',', $data));
//            }
//            $ret = $this->dao->count([['pid', 'in', $data]]);
//
//            if ($ret > 0) {
//                throw new AdminException('该部门下存在子部门，请先删除子部门');
//            }
//            $this->dao->destroy($data);
//        } catch (\Throwable $e) {
//            throw new AdminException($e->getMessage());
//        }
//    }

}
