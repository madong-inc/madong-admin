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

class SystemOperateLog extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_operate_log';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        "tenant_id",
        'name',
        'app',
        'ip',
        'ip_location',
        'browser',
        'os',
        'url',
        'class_name',
        'action',
        'method',
        'param',
        'result',
        'created_at',
        'updated_at',
        'user_name',
    ];

    protected $casts = [
        'param'  => 'array',
        'result' => 'array',
    ];
}
