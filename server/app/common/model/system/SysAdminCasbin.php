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
use core\context\TenantContext;
use core\casbin\model\RuleModel;

/**
 * 关联模型-策略表
 *
 * @author Mr.April
 * @since  1.0
 */
class SysAdminCasbin extends Pivot
{
    protected $table = 'sys_admin_casbin';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'admin_casbin_id',
    ];

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
