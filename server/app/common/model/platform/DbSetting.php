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

namespace app\common\model\platform;

use madong\admin\abstract\BaseModel;

/**
 * 多数据源
 *
 * @author Mr.April
 * @since  1.0
 */
class DbSetting extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'mt_db_setting';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        "id",
        "name",
        "description",
        "driver",
        "host",
        "port",
        "database",
        "username",
        "password",
        "prefix",
        "variable",
        "enabled",
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
    ];

    /**
     * 特殊表使用默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
