<?php
declare(strict_types=1);

namespace app\model\member;

use app\enum\common\EnabledStatus;
use app\enum\member\ThirdPartyPlatform;
use core\base\BaseModel;

/**
 * 会员第三方登录模型
 *
 * @property int|mixed $enabled*/
class MemberThirdParty extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'member_third_party';

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
        'platform',
        'openid',
        'unionid',
        'nickname',
        'avatar',
        'gender',
        'country',
        'province',
        'city',
        'access_token',
        'refresh_token',
        'enabled',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
        'platform_text',
        'enabled_text',
    ];

    /**
     * 获取平台文本
     */
    public function getPlatformTextAttribute(): string
    {
        return ThirdPartyPlatform::tryFrom($this->platform)?->text() ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getEnabledTextAttribute(): string
    {
        return EnabledStatus::tryFrom($this->enabled)?->label() ?? '未知';
    }

    /**
     * 关联会员
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
