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

use app\common\model\system\SystemMenu;
use madong\basic\BaseDao;

class SystemMenuDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemMenu::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        $order='sort';
        return parent::selectList($where, $field, $page, $limit, $order, [], $search, $withoutScopes);
    }
}
