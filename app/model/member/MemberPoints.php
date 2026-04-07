<?php
declare(strict_types=1);

namespace app\model\member;



use core\base\BaseModel;

/**
 * 会员积分模型
 */
class MemberPoints extends BaseModel
{

    protected $table = 'member_points';

    protected $fillable = [
        'id',
        'member_id',
        'points',
        'balance',
        'points_after',
        'type',
        'source',
        'remark',
        'operator',
        'order_id',
        'created_at',
        'create_time',
    ];

    /**
     * 主键类型
     */
    protected $keyType = 'string';

    /**
     * 是否自增
     */
    public $incrementing = false;

    protected static function booted(): void
    {

    }

    protected $casts = [
        'points' => 'integer',
        'balance' => 'integer',
        'points_after' => 'integer',
        'type' => 'integer',
    ];

    /**
     * 关联会员模型
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

}

