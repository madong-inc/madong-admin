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
 * 租户订阅套餐-中间表
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantPackage extends Pivot
{

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $table = 'mt_tenant_package';

    protected $fillable = [
        "tenant_id",
        "subscription_id",
    ];


    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
