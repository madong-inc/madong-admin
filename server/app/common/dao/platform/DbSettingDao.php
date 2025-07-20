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

namespace app\common\dao\platform;

use app\common\model\platform\DbSetting;
use core\abstract\BaseDao;

/**
 * 数据中心
 *
 * @author Mr.April
 * @since  1.0
 */
class DbSettingDao extends BaseDao
{

    protected function setModel(): string
    {
        return DbSetting::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        $order = 'is_default desc';
        return parent::selectList($where, $field, $page, $limit, $order, $with, $search, $withoutScopes)->makeHidden(['username', 'password']);
    }
}
