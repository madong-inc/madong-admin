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

use app\common\model\system\SysAdmin;
use app\common\model\system\SysAdminTenant;
use app\common\model\system\SysAdminTenantRole;
use app\common\model\system\SysRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use madong\admin\abstract\BaseModel;

/**
 * 账套中心
 *
 * @property mixed $id
 * @property mixed $code
 * @property mixed $db_name
 * @property mixed $isolation_mode
 * @author Mr.April
 * @since  1.0
 */
class Tenant extends BaseModel
{

    /**
     * 启用软删除
     */
    use SoftDeletes;

    protected static function booted()
    {

    }

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = [];

    protected $table = 'mt_tenant';

    protected $appends = ['created_date', 'updated_date'];

    protected $casts = [
        'isolation_mode' => 'integer',
        'id'             => 'string',
    ];

    protected $fillable = [
        'id',
        'db_name',
        'code',
        'type',
        'contact_person',
        'contact_phone',
        'company_name',
        'license_number',
        'isolation_mode',
        'address',
        'description',
        'domain',
        'enabled',
        'expired_at',
        'deleted_at',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    // 关联管理员（多对多，通过中间表 admin_tenant）
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(SysAdmin::class, SysAdminTenant::class, 'tenant_id', 'admin_id')
            ->using(SysAdminTenant::class);
    }

    /**
     * 关联租户套餐-通过中间表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(TenantSubscription::class, TenantPackage::class, 'tenant_id', 'subscription_id');
    }

    /**
     * 特殊表使用默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
