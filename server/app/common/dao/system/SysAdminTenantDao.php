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

use app\common\cntext\TenantContext;
use app\common\model\system\SysAdmin;
use app\common\model\system\SysAdminTenant;
use app\common\scopes\global\TenantScope;
use madong\admin\abstract\BaseDao;

class SysAdminTenantDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysAdminTenant::class;
    }

    /**
     * 列表查询
     *
     * @param array      $where
     * @param string     $field
     * @param int        $page
     * @param int        $limit
     * @param string     $order
     * @param array      $with
     * @param bool       $search
     * @param array|null $withoutScopes
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        if (empty($withoutScopes)) {
            $withoutScopes = [TenantScope::class];
        }
        if (empty($with)) {
            $with = ['tenant' => function ($query) use ($where) {
                //可以添加查询字段
            }];
        }
        return parent::selectList($where, $field, $page, $limit, $order, $with, $search, $withoutScopes);
    }

}
