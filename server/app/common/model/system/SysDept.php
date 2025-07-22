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

/**
 * 部门模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysDept extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_dept';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'pid',
        'tenant_id',
        'level',
        'code',
        'name',
        'main_leader_id',
        'phone',
        'enabled',
        'sort',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'remark',
    ];

    /**
     * 部门名称-搜索器
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

    public function scopePid($query, $value)
    {
        if (!empty($value)) {
            if (is_string($value)) {
                $value = array_map('trim', explode(',', $value));
            }
            if (is_array($value)) {
                $query->whereIn('pid', $value);
            } else {
                $query->where('pid', $value);
            }
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

    public function leader(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SysAdmin::class, SysDeptLeader::class, 'dept_id', 'admin_id');
    }

}
