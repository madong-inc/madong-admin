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

use app\common\scopes\global\AccessScope;
use app\common\scopes\global\TenantScope;
use madong\basic\BaseModel;

/**
 * 用户信息模型
 */
class SystemUser extends BaseModel
{

    // 完整数据库表名称
    protected $table = 'system_user';
    // 主键
    protected $primaryKey = 'id';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'tenant_id',
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
        'created_dept'
    ];

    /**
     * 模型的「引导」方法。
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
        static::addGlobalScope(new AccessScope);
    }

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
     * 定义与 UserRole 的关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SystemUserRole::class, 'user_id', 'id');
    }

    /**
     * 通过中间表关联角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_user_role', 'user_id', 'role_id');
    }

    /**
     * 通过中间表关联岗位
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemPost::class, 'system_user_post', 'user_id', 'post_id');
    }

    /**
     * 关联部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function depts(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SystemDept::class, 'id', 'dept_id');
    }
}
