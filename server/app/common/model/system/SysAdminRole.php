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
 * 用户-关联角色模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysAdminRole extends Pivot
{
    protected $table = 'sys_admin_role';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'role_id',
    ];

}
