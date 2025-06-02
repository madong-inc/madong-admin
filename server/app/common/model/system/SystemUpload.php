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
 * 附件模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemUpload extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'system_upload';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        "tenant_id",
        'url',
        'size',
        'size_info',
        'hash',
        'filename',
        'original_filename',
        'base_path',
        'path',
        'ext',
        'content_type',
        'platform',
        'th_url',
        'th_filename',
        'th_size',
        'th_size_info',
        'th_content_type',
        'object_id',
        'object_type',
        'attr',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * createds
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function createds(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'created_by')->select('id', 'real_name as created_name');
    }

    public function updateds(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'updated_by')->select('id', 'real_name as updated_name');
    }

}
