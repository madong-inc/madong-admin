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

use app\common\dao\system\SysMenuDao;
use app\common\model\system\SysMenu;
use madong\admin\abstract\BaseService;
use madong\helper\Arr;
use madong\helper\Tree;
use support\Container;

/**
 * @method save(array $data)
 */
class SysMenuService extends BaseService
{

    /**
     * 缓存key
     */
    const CACHE_KEY = 'menu_list';

    /**
     * 所有权限
     */
    const CACHE_ALL_AUTHS_DATA = 'all_auths_data';

    /**
     * 所有菜单
     */
    const CACHE_ALL_MENUS_DATA = 'all_menus_data';

    public function __construct()
    {

        $this->dao = Container::make(SysMenuDao::class);
    }

    /**
     * 输出所有权限
     *
     * @param array $menuIds
     *
     * @return array
     * @throws \Exception
     */
    public function getAllAuth(array $menuIds = []): array
    {
        // 1. 优先从缓存获取所有权限数据（自动处理缓存未命中时的数据库查询）
        $allAuthItems = $this->cacheDriver()->remember(
            self::CACHE_ALL_AUTHS_DATA,
            function () {
                // 缓存未命中时，从数据库获取数据（仅查询需要的字段）
                return $this->dao->getModel()
                    ->where('path', '<>', '')
                    ->where('type', '=', 4)
                    ->get(['id', 'path', 'methods'])
                    ->toArray();
            }
        );

        // 2. 如果传入了 $menuIds，则筛选出对应的权限项
        if (!empty($menuIds)) {
            $allAuthItems = array_filter($allAuthItems, function ($item) use ($menuIds) {
                return in_array($item['id'], $menuIds);
            });
        }

        // 3. 格式化输出（复用现有方法）
        return $this->formatAuthDataFromItems($allAuthItems);
    }

    /**
     * 根据授权项数组格式化输出为 ['METHOD' => ['/path1', '/path2'], ...] 的形式。
     *
     * @param array $allAuthItems 授权项数组，每个元素包含 'id', 'path', 'methods'
     *
     * @return array 格式化后的授权数据
     */
    private function formatAuthDataFromItems(array $allAuthItems): array
    {
        $allAuth = [];
        foreach ($allAuthItems as $item) {
            // 假设 'methods' 是逗号分隔的字符串，如 "GET,POST"
            $methodArray = explode(',', $item['methods']); // 转换为数组

            $pathKey = strtolower(trim(str_replace(' ', '', $item['path'])));

            foreach ($methodArray as $method) {
                $methodKey             = strtolower(trim($method));
                $allAuth[$methodKey][] = $pathKey;
            }
        }

        // 如果需要，可以对每个方法的路径数组进行去重
        foreach ($allAuth as &$paths) {
            $paths = array_unique($paths);
        }
        unset($paths); // 解除引用
        return $allAuth;
    }

    /**
     * 获取所有菜单，优先从缓存读取，并根据 $where 条件筛选。
     *
     * @param array $where 筛选条件数组，键为字段名，值为条件值或数组（用于 IN 条件）或操作符数组（如 ['>', 5]）
     *
     * @return array 处理后的菜单树形结构
     */
    public function getAllMenus(array $where = [], $mode = 'menu', $isTree = true): array
    {
        // 1. 优先从缓存获取所有菜单数据
        $allMenus = $this->cacheDriver()->remember(
            self::CACHE_ALL_MENUS_DATA, // 缓存键名
            function () {
                $list = $this->dao->getModel()
                    ->where('enabled', 1)
//                    ->whereIn('type', [1, 2])// 目录 & 菜单类型 全量保存缓存条件放到缓存输出
                    ->orderBy('sort', 'asc')
                    ->get();
                foreach ($list as $item) {
                    $item->set('name', $item->code);
                    $item->set('meta', SysMenu::getMetaAttribute($item));
                }
                $list->makeVisible(['id', 'pid', 'type', 'sort', 'redirect', 'path', 'name', 'meta', 'component']);
                return $list->toArray();
            }
        );

        // 2. 根据 $where 条件筛选菜单数据
        $menusToProcess = Arr::filterByWhere($allMenus, $where);

        if ($mode == 'code') {
            return array_column($menusToProcess, 'code');
        }
        if (!$isTree) {
            return $menusToProcess;
        }

        // 构建树形结构
        return $this->buildMenuTree($menusToProcess);

    }

    /**
     * 构建菜单的树形结构。
     *
     * @param array $formattedMenus 已处理的菜单数据
     *
     * @return array 树形结构的菜单数据
     */
    protected function buildMenuTree(array $formattedMenus): array
    {
        $tree = new Tree($formattedMenus);
        return $tree->getTree();
    }

}
