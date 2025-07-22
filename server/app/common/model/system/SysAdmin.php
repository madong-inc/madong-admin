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

use app\common\model\platform\Tenant;
use app\common\scopes\global\TenantScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use core\abstract\BaseModel;
use core\context\TenantContext;
use core\casbin\model\RuleModel;
use support\Db;

/**
 * 后台管理员-模型
 */
class SysAdmin extends BaseModel
{
    /**
     * 启用软删除
     */
    use SoftDeletes;

    // 完整数据库表名称
    protected $table = 'sys_admin';
    // 主键
    protected $primaryKey = 'id';

    protected $appends = ['created_date', 'updated_date'];

    protected $casts = [
        'backend_setting' => 'array',
        'id'              => 'string',
    ];

    protected $fillable = [
        'id',
        'user_name',
        'real_name',
        'nick_name',
        'password',
        'is_super',
        'mobile_phone',
        'email',
        'avatar',
        'signed',
        'dashboard',
        'dept_id',
        'enabled',
        'login_ip',
        'login_time',
        'backend_setting',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'sex',
        'remark',
        'birthday',
        'tel',
        'is_locked',
    ];

    /**
     * 账号-搜索器
     *
     * @param $query
     * @param $value
     */
    public function scopeUserName($query, $value)
    {
        if (!empty($value)) {
            $query->where('user_name', 'like', $value . '%');
        }
    }

    /**
     * 用户昵称-搜索器
     *
     * @param $query
     * @param $value
     */
    public function scopeRealName($query, $value)
    {
        if ($value !== '') {
            $query->where('real_name', 'like', $value . '%');
        }
    }

    /**
     * 用户参数-解析
     *
     * @param $value
     *
     * @return mixed
     */
    public function getBackendSetting($value): mixed
    {
        return json_decode($value ?? '', true);
    }

    /**
     * 用户参数-转换
     *
     * @param $value
     *
     * @return bool|string
     */
    public function setBackendSettingAttr($value): bool|string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 关联-用户部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function depts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SysDept::class, SysAdminDept::class, 'admin_id', 'dept_id');
    }

    /**
     * 关联-用户职位
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SysPost::class, SysAdminPost::class, 'admin_id', 'post_id');
    }

    /**
     * 通过中间表关联角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SysRole::class, SysAdminRole::class, 'admin_id', 'role_id');
    }

    /**
     * 关联-用户策略表
     */
    public function casbin(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(RuleModel::class, SysAdminCasbin::class, 'admin_id', 'admin_casbin_id', 'id', 'v0');
    }

}
