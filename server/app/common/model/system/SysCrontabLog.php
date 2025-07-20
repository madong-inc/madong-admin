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
 *定时任务日志
 */
class SysCrontabLog extends BaseModel
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_crontab_log';

    /**
     * The primary key associated with the table
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'crontab_id',
        'target',
        'log',
        'return_code',
        'running_time',
        'created_at',
        'updated_at',
    ];

    /**
     * 获取 create_time 属性的访问器。
     *
     * @param $value
     *
     * @return string
     */
    public function getCreateTimeAttr($value): string
    {
        return date('Y-m-d H:i:s', $value); // 将时间戳格式化为日期时间字符串
    }

    public function getRunningTimeAttr($value): string
    {
        return number_format($value, 2) . 'ms'; // 将时间戳格式化为日期时间字符串
    }

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}




















