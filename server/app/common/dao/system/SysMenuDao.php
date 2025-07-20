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

use app\common\model\system\SysMenu;
use core\abstract\BaseDao;

/**
 *
 * 平台菜单
 * @author Mr.April
 * @since  1.0
 */
class SysMenuDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysMenu::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        $order='sort';
        return parent::selectList($where, $field, $page, $limit, $order, [], $search, $withoutScopes);
    }
}
