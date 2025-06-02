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

use app\common\model\system\SystemRole;
use madong\basic\BaseDao;

class SystemRoleDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemRole::class;
    }

    /**
     * 通过角色获取菜单
     *
     * @param array $ids
     *
     * @return array
     * @throws \Exception
     */
    public function getMenuIdsByRoleIds(array $ids = []): array
    {
        if (empty($ids)) {
            return [];
        }
        $where = ['id' => $ids];
        return $this->selectList($where, '*', 0, 0, '', ['menus' => function ($query) {
            $query->where('enabled', 1);
        }], true)->toArray();
    }
}
