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

use app\dao\system\SystemRoleDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;
use think\db\Query;
use think\facade\Db;

/**
 * @method save(array $data)
 */
class SystemRoleService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemRoleDao::class);
    }

    /**
     * 通过角色获取菜单
     *
     * @param array $ids
     *
     * @return array
     */
    public function getMenuIdsByRoleIds(array $ids = []): array
    {
        if (empty($ids)) {
            return [];
        }
        $where = ['id' => $ids];
        return $this->dao->selectList($where, '*', 0, 0, '', ['menus' => function (Query $query) {
            $query->where('enabled', 1)->order('sort', 'asc');
        }], true)->toArray();
    }
//
//    /**
//     * 批量删除角色
//     *
//     * @param array|string $data
//     */
//    public function batchDelete(array|string $data): void
//    {
//        Db::startTrans();
//        try {
//            if (is_string($data)) {
//                $data = array_map('trim', explode(',', $data));
//            }
//            $rolesToDelete = $this->selectList([['id', 'in', $data]], '*', 0, 0, '', [], false);
//            foreach ($rolesToDelete as $item) {
//                if ($item->getData('is_super_admin') == 1) {
//                    throw new AdminException('系统内置角色，不可删除');
//                }
//                $item->delete();
//            }
//            Db::commit();
//        } catch (\Throwable $e) {
//            Db::rollback();
//            throw new AdminException($e->getMessage());
//        }
//    }
}
