<?php
declare(strict_types=1);
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\service\api\web;

use app\api\CurrentMember;
use app\dao\web\MenuDao;
use app\enum\common\EnabledStatus;
use core\base\BaseService;
use support\Container;

/**
 * 菜单服务
 */
class MenuService extends BaseService
{

    // 缓存键前缀
    public const CACHE_PREFIX = 'web_navigation_list';

    // 缓存过期时间（秒）
    public const CACHE_EXPIRE = 3600;

    public function __construct(MenuDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取导航菜单列表（带权限过滤）
     *
     * @return array
     * @throws \Exception
     */
    public function getNavigationList(): array
    {
        // 使用 remember 模式缓存所有菜单
        $cacheKey = self::CACHE_PREFIX . '_all';
//        $allMenus = $this->cacheDriver()->remember($cacheKey, function () {
//            $map = [
//                ['enabled', 'eq', EnabledStatus::ENABLED->value],
//                ['is_show', 'eq', 1],
//            ];
//            $menus = $this->dao->selectList($map, ['*'], 0, 0, 'sort asc, id asc');
//            return $menus->toArray();
//        }, self::CACHE_EXPIRE);
        $map   = [
            ['enabled', 'eq', EnabledStatus::ENABLED->value],
            ['is_show', 'eq', 1],
        ];
        $menus = $this->dao->selectList($map, ['*'], 0, 0, 'sort asc, id asc');
        return $menus->toArray();

        // 获取当前用户权限码
        $userPermissions = $this->getUserPermissions();
        $isLogin         = !empty($userPermissions);

        // 根据权限过滤菜单
        $filteredMenus = [];
        foreach ($allMenus as $menu) {
            $code = $menu['code'] ?? '';
            // code为空表示公开菜单，所有用户可见
            if (empty($code)) {
                $filteredMenus[] = $menu;
                continue;
            }
            // code不为空，需要权限
            // 未登录用户不显示需要权限的菜单
            if (!$isLogin) {
                continue;
            }
            // 登录用户检查是否在权限列表中
            if (in_array($code, $userPermissions, true)) {
                $filteredMenus[] = $menu;
            }
        }

        return $filteredMenus;
    }

    /**
     * 获取当前用户的权限码列表
     *
     * @return array
     */
    private function getUserPermissions(): array
    {
        try {
            /** @var CurrentMember $currentMember */
            $currentMember = Container::make(CurrentMember::class);
            return $currentMember->getPermissions();
        } catch (\Exception) {
            // 未登录或获取权限失败，返回空数组，只显示公开菜单
            return [];
        }
    }

    /**
     * 清除菜单缓存
     *
     * @return void
     */
    public function clearMenuCache(): void
    {
        $this->cacheDriver()->delete(self::CACHE_PREFIX . '_all');
    }
}
