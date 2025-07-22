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
 * 角色-关联策略表模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRoleCasbin extends Pivot
{
    protected $table = 'sys_role_casbin';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'role_casbin_id',
    ];

}
