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

use app\common\model\system\SystemCrontab;
use madong\basic\BaseDao;

class SystemCrontabDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemCrontab::class;
    }

    /**
     * 获取列表
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
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false,?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        $result = parent::selectList($where, $field, $page, $limit, $order, [], $search,$withoutScopes);

        $systemCrontabLogDao = new SystemCrontabLogDao();
        if (!empty($result)) {
            foreach ($result as $item) {
                $item->rule_name .= '';
                $item->logs      = $systemCrontabLogDao->getModel()
                    ->where(['crontab_id' => $item->id])
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }
        return $result;
    }

}
