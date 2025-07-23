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

/**
 * 附件模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRecycleBin extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_recycle_bin';

    protected $appends = ['created_date', 'updated_date', 'operate_name'];

    protected $fillable = [
        'id',
        'original_id',
        'tenant_id',
        'data',
        'table_name',
        'table_prefix',
        'enabled',
        'ip',
        'operate_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',

    ];

//    // 1. 定义访问器/修改器
//    public function getDataAttribute($value)
//    {
//        return json_decode($value, true) ?? [];
//    }
//
//    public function setDataAttribute($value)
//    {
//        $this->attributes['data'] = json_encode($value);
//    }

    /**
     * 动态数据库链接(主库)
     *
     * @return \app\common\model\system\SysRecycleBin
     */
    public static function centralConnection(): SysRecycleBin
    {
        return (new static)->setConnection(config('database.default'));
    }

    /**
     * 定义访问器
     *
     * @return null
     */
    public function getOperateNameAttribute(): mixed
    {
        return $this->operate ? $this->operate->operate_name : null;
    }

    /**
     * 关联-操作人
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function operate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SysAdmin::class, 'id', 'operate_id')->select(['id', 'real_name as operate_name']);
    }
}
