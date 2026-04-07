<?php
declare(strict_types=1);

namespace app\model\web;

use app\enum\common\EnabledStatus;
use app\enum\web\MenuTarget;
use core\base\BaseModel;

/**
 * 友情链接模型
 */
class Link extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'web_link';

    /**
     * 数据表主键
     */
    protected $primaryKey = 'id';

    /**
     * 可批量赋值的字段
     */
    protected $fillable = [
        'id',
        'name',
        'url',
        'logo',
        'description',
        'category',
        'sort',
        'target',
        'click_count',
        'enabled',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'enabled' => 'integer',
        'sort' => 'integer',
        'click_count' => 'integer',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
        'target_text',
        'enabled_text',
    ];

    /**
     * 获取目标窗口文本
     */
    public function getTargetTextAttribute(): string
    {
        $target = MenuTarget::tryFrom((int)$this->target);
        return $target?->label() ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getEnabledTextAttribute(): string
    {
        $status = EnabledStatus::tryFrom((int)$this->enabled);
        return $status?->label() ?? '未知';
    }
}
