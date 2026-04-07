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

namespace app\model\logs;

use app\model\system\Admin;
use core\base\BaseModel;

class LoginLog extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_login_log';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'user_id',
        'ip',
        'ip_location',
        'os',
        'browser',
        'status',
        'message',
        'login_time',
        'key',
        'created_at',
        'expires_at',
        'updated_at',
        'deleted_at',
        'remark',
    ];

    protected $casts = [];

    /**
     * 关联管理员
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
