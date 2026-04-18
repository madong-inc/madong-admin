<?php
declare(strict_types=1);
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

namespace app\model\system;

use app\model\message\Message;
use app\model\org\Dept;
use app\model\org\Post;
use core\base\BaseModel;
use core\casbin\model\RuleModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 后台管理员-模型
 */
class Admin extends BaseModel
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
     * 判断是否为超级管理员
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return (bool)$this->is_super;
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
     * 关联-主归属信息（1:1）
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mainInfo(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdminMain::class, 'admin_id', 'id');
    }

    /**
     * 关联-用户部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function depts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Dept::class, AdminDept::class, 'admin_id', 'dept_id');
    }

    /**
     * 关联-用户职位
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Post::class, AdminPost::class, 'admin_id', 'post_id');
    }

    /**
     * 通过中间表关联角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, AdminRole::class, 'admin_id', 'role_id');
    }



    /**
     * 管理消息-列表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function message(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id');
    }

}
