<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitcode.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\common\model\system;

use madong\basic\BaseModel;

/**
 * 系统公告
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemNotice extends BaseModel
{

    protected $table = 'system_notice';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        "tenant_id",
        'message_id',
        'title',
        'type',
        'content',
        'enabled',
        'created_dept',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'remark',
    ];

}
