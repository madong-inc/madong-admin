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

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 关联模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysDeptLeader extends Pivot
{
    protected $table = 'sys_dept_leader';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'dept_id',
        'admin_id',
    ];

}
