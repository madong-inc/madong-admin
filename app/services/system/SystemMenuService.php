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

use app\dao\system\SystemMenuDao;
use madong\basic\BaseService;
use madong\utils\Tree;
use support\Container;
use support\Request;

/**
 * @method save(array $data)
 * @method getColumn(array $map, string $string)
 */
class SystemMenuService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemMenuDao::class);
    }

    /**
     * 获取后台管理菜单
     *
     * @param array $where
     *
     * @return array
     */
//    public function getAdminMenu(array $where = []): array
//    {
//        $list = $this->dao->selectList($where, '*', 0, 0, 'sort asc', [], true);
//        foreach ($list as $item) {
//            $item->set('meta', $item->meta);
//        }
//        $list->visible(['id', 'pid', 'type', 'sort', 'redirect', 'path', 'name', 'meta', 'component']);
//        $tree = new Tree($list);
//        return $tree->getTree();
//    }
//
//    public function getButtonPermissionsList(array &$where = [])
//    {
//        $where['enabled'] = 1;
//
//    }
//
//    /**
//     * 通过角色筛选菜单ID集合并去重
//     *
//     * @param array $roleData
//     *
//     * @return array
//     */
//    public function filterMenuIds(array &$roleData): array
//    {
//        $idBundle = [];
//        foreach ($roleData as $val) {
//            foreach ($val['menus'] as $menu) {
//                $idBundle[] = $menu['id'];
//            }
//        }
//        unset($roleData);
//        return array_unique($idBundle);
//    }


}
