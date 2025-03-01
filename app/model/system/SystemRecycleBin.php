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

namespace app\model\system;

use madong\basic\BaseLaORMModel;

/**
 * 回收站模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemRecycleBin extends BaseLaORMModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_recycle_bin';

    protected $appends = ['operate_name'];

    protected  $fillable = [
        'id',
        'data',
        'table_name',
        'table_prefix',
        'enabled',
        'ip',
        'operate_id',
        'create_time'
    ];


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
        return $this->hasOne(SystemUser::class, 'id', 'operate_id')->select(['id', 'real_name as operate_name']);
    }
}
