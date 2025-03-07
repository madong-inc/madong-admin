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

use app\model\system\SystemCrontab;
use app\services\system\SystemCrontabLogService;
use madong\basic\BaseDao;
use think\Container;

class SystemCrontabDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemCrontab::class;
    }

    /**
     * 获取列表
     *
     * @param array  $where
     * @param string $field
     * @param int    $page
     * @param int    $limit
     * @param string $order
     * @param array  $with
     * @param bool   $search
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false): ?\Illuminate\Database\Eloquent\Collection
    {
        $result = parent::selectList($where, $field, $page, $limit, $order, [], $search);

        $systemCrontabLogDao = new SystemCrontabLogDao();
        if (!empty($result)) {
            foreach ($result as $item) {
                $item->rule_name .= '';
                $item->logs      = $systemCrontabLogDao->getModel()
                    ->where(['crontab_id' => $item->id])
                    ->orderBy('create_time', 'desc')
                    ->first();
            }
        }
        return $result;
    }

}
