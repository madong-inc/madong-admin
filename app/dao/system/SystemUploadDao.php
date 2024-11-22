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

use app\model\system\SystemUpload;
use madong\basic\BaseDao;

class SystemUploadDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemUpload::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false)
    {
        if (Config('app.model_type', 'thinkORM') == 'thinkORM') {
            return parent::selectList($where, $field, $page, $limit, $order, ['created', 'updated'], $search)->toArray();
        } else {
            return parent::selectList($where, $field, $page, $limit, $order, ['createds', 'updateds'], $search);
        }
    }
}
