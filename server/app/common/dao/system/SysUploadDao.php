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

use app\common\model\system\SysUpload;
use core\abstract\BaseDao;

/**
 * 上传
 *
 * @author Mr.April
 * @since  1.0
 */
class SysUploadDao extends BaseDao
{

    protected function setModel(): string
    {
        return SysUpload::class;
    }

    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        //重写追加两个关联模型
        return parent::selectList($where, $field, $page, $limit, $order, ['createds', 'updateds'], $search, $withoutScopes);
    }
}
