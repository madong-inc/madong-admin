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

use madong\admin\abstract\BaseModel;

class SysRoute extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_route';

    protected $appends = ['created_date', 'updated_date'];

    protected $casts = [
        'query'            => 'array',
        'header'           => 'array',
        'response'         => 'array',
        'request_example'  => 'array',
        'response_example' => 'array',
    ];

    protected $fillable = [
        'id',
        'cate_id',
        'app_name',
        'name',
        'describe',
        'path',
        'method',
        'file_path',
        'action',
        'query',
        'header',
        'request',
        'request_type',
        'response',
        'request_example',
        'response_example',
        'created_at',
        'updated_at',
    ];

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
