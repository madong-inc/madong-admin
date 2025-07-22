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
 * 限流模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRateLimiter extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_rate_limiter';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        "id",
        "name",
        "enabled",
        "priority",
        "match_type",
        "conditions",
        "http_methods",
        "path",
        "limit_type",
        "limit_value",
        "period",
        "ttl",
        "message",
        "created_by",
        "updated_by",
        "created_at",
        "updated_at",
    ];

}
