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

use madong\basic\BaseModel;

/**
 * 角色模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemRole extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_role';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        "tenant_id",
        'pid',
        'name',
        'code',
        'role_type',
        'data_scope',
        'enabled',
        'sort',
        'remark',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Id搜索
     */
    public function scopeId($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } else {
            $query->where('id', $value);
        }
    }

    /**
     * 角色名称-搜索器
     *
     * @param $query
     * @param $value
     */
    public function scopeName($query, $value)
    {
        if (!empty($value)) {
            $query->where('name', 'like', $value . '%');
        }
    }

    /**
     * 状态-搜索器
     *
     * @param $query
     * @param $value
     */
    public function scopeStatus($query, $value)
    {
        if ($value !== '') {
            $query->where('status', $value);
        }
    }

    /**
     * 通过中间表获取菜单
     */
    public function menus(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemMenu::class, SystemRoleMenu::class, 'role_id', 'menu_id')->orderBy('sort');
    }

    /**
     * 通过中间表获取部门
     */
    public function depts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemDept::class, SystemRoleDept::class, 'role_id', 'dept_id');
    }

    /**
     * 通过中间表获取部门
     */
    public function scopes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemDept::class, SystemRoleScopeDept::class, 'role_id', 'dept_id');
    }

}
