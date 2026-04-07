<?php
declare(strict_types=1);

namespace app\model\member;

use core\base\BaseModel;

/**
 * 会员签到模型
 */
class MemberSign extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'member_sign';

    /**
     * 数据表主键
     */
    protected $primaryKey = 'id';

    /**
     * 可批量赋值的字段
     */
    protected $fillable = [
        'id',
        'member_id',
        'sign_date',
        'points',
        'continuous_days',
        'device_ip',
        'device_ua',
        'created_at',
        'updated_at'
    ];

    /**
     * 关联会员
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}