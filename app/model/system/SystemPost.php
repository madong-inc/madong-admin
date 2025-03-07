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

use madong\basic\BaseModel;

/**
 * 岗位模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemPost extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_post';

    protected $appends = ['create_date', 'update_date'];

    protected $fillable = [
        'id',
        'code',
        'name',
        'sort',
        'enabled',
        'created_by',
        'updated_by',
        'create_time',
        'update_time',
        'delete_time',
        'remark',
    ];

}
