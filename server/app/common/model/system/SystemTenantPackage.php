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
 * 租户套餐模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemTenantPackage extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_tenant_package';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'name',
        'remark',
        'enabled',
        'created_dept',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            //删除主模型关联删除中间表  注意不能使用delete会删除目标数据
            $model->menus()->detach();
        });
    }

    /**
     * 通过中间表获取菜单
     */
    public function menus(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SystemMenu::class, SystemTenantPackageMenu::class, 'package_id', 'menu_id');
    }
}
