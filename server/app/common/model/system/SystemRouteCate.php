<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */

namespace app\common\model\system;

use madong\basic\BaseModel;

class SystemRouteCate extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_route_cate';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        "tenant_id",
        'pid',
        'app_name',
        'name',
        'path',
        'sort',
        'created_at',
        'updated_at',
    ];
}
