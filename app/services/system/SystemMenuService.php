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
     * 获取菜单树
     *
     * @param array  $where
     * @param string $order
     *
     * @return array|null
     */
//    public function menuTree(array $where, string $order = 'sort'): ?array
//    {
//        $list = $this->dao->selectList($where, '*', 0, 0, $order, [], true);
//        $tree = new Tree($list);
//        return $tree->getTree();
//    }

    /**
     * 通过角色筛选菜单ID集合并去重
     *
     * @param array $roleData
     *
     * @return array
     */
    public function filterMenuIds(array &$roleData): array
    {
        $idBundle = [];
        foreach ($roleData as $val) {
            foreach ($val['menus'] as $menu) {
                $idBundle[] = $menu['id'];
            }
        }
        unset($roleData);
        return array_unique($idBundle);
    }
}
