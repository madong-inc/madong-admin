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

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use madong\basic\BaseModel;

/**
 * 租户模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemTenant extends BaseModel
{

    /**
     * 启用软删除
     */
    use SoftDeletes;

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_tenant';

    protected $appends = ['created_date', 'updated_date', 'expired_date', 'name'];

    protected $hidden = [];

    protected $fillable = [
        "id",
        "tenant_id",
        "package_id",
        "contact_user_name",
        "contact_phone",
        "company_name",
        "license_number",
        "address",
        "intro",
        "domain",
        "account_count",
        "enabled",
        "deleted_at",
        "created_dept",
        "created_by",
        "created_at",
        "expired_at",
        "remark",
        "updated_by",
        "updated_at",
    ];



    /**
     * 追加过期时间
     *
     * @return string|null
     */
    public function getExpiredDateAttribute(): ?string
    {
        if ($this->getAttribute('expired_at')) {
            try {
                $timestamp = $this->getRawOriginal('expired_at');
                if (empty($timestamp)) {
                    return null;
                }
                $carbonInstance = Carbon::createFromTimestamp($timestamp);
                return $carbonInstance->setTimezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function getNameAttribute(): ?string
    {
        if ($this->getAttribute('company_name')) {
            try {
                return $this->getRawOriginal('company_name');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
