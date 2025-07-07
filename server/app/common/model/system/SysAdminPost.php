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
class SysAdminPost extends Pivot
{
    protected $table = 'sys_admin_post';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'post_id',
    ];

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
