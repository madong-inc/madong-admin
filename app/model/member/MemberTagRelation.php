<?php
declare(strict_types=1);

namespace app\model\member;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 会员标签关系模型
 */
class MemberTagRelation extends Pivot
{
    /**
     * 数据表名称
     */
    protected $table = 'member_tag_relation';

    /**
     * 可批量赋值的字段
     */
    protected $fillable = [
        'member_id',
        'tag_id',
    ];

    /**
     * 关联会员
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    /**
     * 关联标签
     */
    public function tag(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MemberTag::class, 'tag_id', 'id');
    }
}