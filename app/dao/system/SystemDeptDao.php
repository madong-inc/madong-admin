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

use app\model\system\SystemDept;
use madong\basic\BaseDao;

class SystemDeptDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemDept::class;
    }

    /**
     * get
     *
     * @param            $id
     * @param array|null $field
     * @param array|null $with
     * @param string     $order
     *
     * @return \app\model\system\SystemDept|null
     */
    public function get($id, ?array $field = [], ?array $with = [], string $order = ''): ?SystemDept
    {
        $model = parent::get($id, ['*'], ['leader']);
        if (!empty($model)) {
            $leader = $model->getData('leader');
            $model->set('leader_id_list', []);
            if (!empty($leader)) {
                $model->set('leader_id_list', array_column($leader->toArray() ?? [], 'id'));
            }
        }
        return $model;
    }
}
