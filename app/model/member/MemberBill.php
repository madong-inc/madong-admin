<?php
declare(strict_types=1);

namespace app\model\member;

use core\base\BaseModel;
use app\enum\member\BillType;
use app\enum\member\BillCategory;
use app\enum\member\BillStatus;

/**
 * 会员账单模型
 */
class MemberBill extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'member_bill';

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
        'type',
        'category',
        'amount',
        'balance',
        'description',
        'order_sn',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
        'type_text',
        'category_text',
        'status_text',
    ];

    /**
     * 获取类型文本
     */
    public function getTypeTextAttribute(): string
    {
        return BillType::tryFrom($this->type)?->text() ?? '未知';
    }

    /**
     * 获取分类文本
     */
    public function getCategoryTextAttribute(): string
    {
        return BillCategory::tryFrom($this->category)?->text() ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute(): string
    {
        return BillStatus::tryFrom($this->status)?->text() ?? '未知';
    }

    /**
     * 关联会员
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
