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

use Illuminate\Support\Carbon;
use madong\admin\abstract\BaseModel;

/**
 * 限访模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRateRestrictions extends BaseModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $table = 'sys_rate_restrictions';

    protected $appends = ['created_date', 'updated_date', 'start_date', 'end_date'];

    protected $fillable = [
        "id",
        "name",
        "enabled",
        "priority",
        "match_type",
        "methods",
        "path",
        "message",
        "start_time",
        "end_time",
        "created_by",
        "updated_by",
        "created_at",
        "updated_at",
    ];

    /**
     * 追加起始时间
     *
     * @return string|null
     */
    public function getStartDateAttribute(): ?string
    {
        if ($this->getAttribute('start_time')) {
            try {
                $timestamp = $this->getRawOriginal('start_time');
                if (empty($timestamp)) {
                    return null;
                }
                $carbonInstance = Carbon::createFromTimestamp($timestamp);
                return $carbonInstance->setTimezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function getEndDateAttribute(): ?string
    {
        if ($this->getAttribute('end_time')) {
            try {
                $timestamp = $this->getRawOriginal('end_time');
                if (empty($timestamp)) {
                    return null;
                }
                $carbonInstance = Carbon::createFromTimestamp($timestamp);
                return $carbonInstance->setTimezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 特殊表使用默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
