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

use core\abstract\BaseModel;
use core\context\TenantContext;
use core\casbin\model\RuleModel;

/**
 * 角色模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRole extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_role';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'tenant_id',
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
        return $this->belongsToMany(SysMenu::class, SysRoleMenu::class, 'role_id', 'menu_id')->orderBy('sort');
    }

    /**
     * 通过中间表获取部门
     */
    public function depts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SysDept::class, SysRoleDept::class, 'role_id', 'dept_id');
    }

    /**
     * 通过中间表获取部门
     */
    public function scopes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SysDept::class, SysRoleScopeDept::class, 'role_id', 'dept_id');
    }

    /**
     * 关联casbin 策略
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function casbin(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        $tenantId = config('tenant.enabled', false) ? 'domain:' . TenantContext::getTenantId() : 'default';
        return $this->belongsToMany(RuleModel::class, SysRoleCasbin::class, 'role_id', 'role_casbin_id', 'id', 'v0')->where('v1', $tenantId)->where('ptype', 'p');
    }

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
