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

namespace app\model\system;

use madong\basic\BaseLaORMModel;

class SystemOperateLog extends BaseLaORMModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_operate_log';

    protected $appends = ['create_date', 'update_date'];

    protected $fillable = [
        'id',
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
        'create_time',
        'user_name',
    ];

}
