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

use madong\admin\abstract\BaseModel;

/**
 * 租户-会话
 *
 * @author Mr.April
 * @since  1.0
 */
class TenantSession extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'mt_tenant_session';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        "id",
        "key",
        "admin_id",
        "tenant_id",
        "token",
        "expire_at",
        "created_at",
        "updated_at",
    ];


    /**
     * 关联租户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tenant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
