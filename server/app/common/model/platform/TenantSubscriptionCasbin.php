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

namespace app\common\model\platform;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 关联模型-策略表
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSubscriptionCasbin extends Pivot
{

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    public $incrementing=false;

    protected $table = 'mt_tenant_subscription_casbin';


    protected $fillable = [
        "subscription_id",
        "subscription_casbin_id",
    ];


    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
