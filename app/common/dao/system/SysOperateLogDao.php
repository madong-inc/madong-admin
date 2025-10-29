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

use app\common\model\system\SysOperateLog;
use core\abstract\BaseDao;

/**
 * 行为日志Dao
 *
 * @author Mr.April
 * @since  1.0
 */
class SysOperateLogDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysOperateLog::class;
    }

    /**
     * 操作日志列表
     *
     * @param array        $where
     * @param string|array $field
     * @param int          $page
     * @param int          $limit
     * @param string       $order
     * @param array        $with
     * @param bool         $search
     * @param array|null   $withoutScopes
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function selectList(array $where, string|array $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        //注意不要输出result 要不然深度递归会卡死
        return parent::selectList($where, $field, $page, $limit, $order, $with, $search, $withoutScopes)->makeHidden(['result']);
    }
}
