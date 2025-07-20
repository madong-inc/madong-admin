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

use core\abstract\BaseModel;

/**
 * 岗位模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysPost extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_post';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'tenant_id',
        'dept_id',
        'code',
        'name',
        'sort',
        'enabled',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'remark',
    ];

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }

}
