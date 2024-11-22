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
 * 附件模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemUpload extends BaseLaORMModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_upload';

    public function getCreatedNameAttribute()
    {
        return $this->createds ? $this->createds->created_name : null; // 获取用户名称
    }

    public function getUpdatedNameAttribute()
    {
        return $this->updateds ? $this->updateds->updated_name : null; // 获取用户名称
    }

    // 定义与 SystemUser 的关系
    public function createds(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'created_by')->select('id', 'real_name as created_name');
    }

    public function updateds(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'updated_by')->select('id', 'real_name as updated_name');
    }

}
