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

namespace app\dao\system;

use app\model\system\SystemRecycleBin;
use app\model\system\SystemUser;
use madong\basic\BaseDao;

class SystemRecycleBinDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemRecycleBin::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false): ?\Illuminate\Database\Eloquent\Collection
    {
        return parent::selectList($where, $field, $page, $limit, $order, ['operate'], $search);
    }

    public function get($id, array|null $field = ['*'], array|null $with = [], string $order = ''): ?SystemRecycleBin
    {
        return parent::get($id, ['*'], ['operate']);
    }

}
