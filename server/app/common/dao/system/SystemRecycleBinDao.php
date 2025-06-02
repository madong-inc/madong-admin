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

use app\common\model\system\SystemRecycleBin;
use madong\basic\BaseDao;

class SystemRecycleBinDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemRecycleBin::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        return parent::selectList($where, $field, $page, $limit, $order, ['operate'], $search, $withoutScopes);
    }

    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null):SystemRecycleBin
    {
        return parent::get($id, ['*'], ['operate'], '', $withoutScopes);
    }

}
