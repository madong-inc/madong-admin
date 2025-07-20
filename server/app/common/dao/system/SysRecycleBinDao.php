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

use app\common\model\system\SysRecycleBin;
use core\abstract\BaseDao;

/**
 * 回收站
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRecycleBinDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysRecycleBin::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        return parent::selectList($where, $field, $page, $limit, $order, ['operate'], $search, $withoutScopes);
    }

    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null): SysRecycleBin
    {
        return parent::get($id, ['*'], ['operate'], '', $withoutScopes);
    }

}
