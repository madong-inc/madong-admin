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

use app\common\model\system\SysMessage;
use core\abstract\BaseDao;

class SysMessageDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysMessage::class;
    }

    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null): ?SysMessage
    {
        return parent::get($id, $field, ['sender'], $order, $withoutScopes);
    }

    public function selectList(array $where, string|array $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        return parent::selectList($where, $field, $page, $limit, 'created_at desc', ['sender'], $search, $withoutScopes);
    }
}
