<?php
declare(strict_types=1);

namespace app\model\member;

use core\base\BaseModel;
use app\enum\member\WithdrawStatus;

/**
 * 会员提现模型
 */
class MemberWithdraw extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'member_withdraw';

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
        'account_id',
        'amount',
        'fee',
        'actual_amount',
        'status',
        'bank_name',
        'bank_account',
        'bank_cardholder',
        'order_sn',
        'remark',
        'audit_remark',
        'created_at',
        'updated_at',
        'audit_at',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
        'status_text',
    ];

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute(): string
    {
        return WithdrawStatus::tryFrom($this->status)?->text() ?? '未知';
    }

    /**
     * 关联会员
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    /**
     * 关联提现账号
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MemberWithdrawAccount::class, 'account_id', 'id');
    }
}
