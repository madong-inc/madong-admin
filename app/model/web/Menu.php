<?php
declare(strict_types=1);

namespace app\model\web;

use app\enum\common\EnabledStatus;
use app\enum\common\YesNoStatus;
use app\enum\system\MenuType;
use app\enum\web\MenuCategory;
use app\enum\web\MenuTarget;
use core\base\BaseModel;

/**
 * 菜单模型
 */
class Menu extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'web_menu';

    /**
     * 数据表主键
     */
    protected $primaryKey = 'id';

    /**
     * 可批量赋值的字段
     */
    protected $fillable = [
        'id',
        'app',
        'category',
        'source',
        'code',
        'name',
        'url',
        'pid',
        'level',
        'type',
        'sort',
        'target',
        'icon',
        'is_show',
        'enabled',
        'permissions',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
        'type_text',
        'target_text',
        'is_show_text',
        'enabled_text',
        'category_text',
    ];

    protected $casts=[
        'id'=>'string',
        'pid'=>'string',
    ];

    /**
     * 获取导航类型文本
     */
    public function getTypeTextAttribute(): string
    {
        $type = MenuType::tryFrom((int)$this->type);
        return $type?->label() ?? '未知';
    }

    /**
     * 获取目标窗口文本
     */
    public function getTargetTextAttribute(): string
    {
        $target = MenuTarget::tryFrom((int)$this->target);
        return $target?->label() ?? '未知';
    }

    /**
     * 获取显示状态文本
     */
    public function getIsShowTextAttribute(): string
    {
        $show = YesNoStatus::tryFrom((int)$this->is_show);
        return $show?->label() ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getEnabledTextAttribute(): string
    {
        $status = EnabledStatus::tryFrom((int)$this->status);
        return $status?->label() ?? '未知';
    }

    /**
     * 获取分类文本
     */
    public function getCategoryTextAttribute(): string
    {
        $category = MenuCategory::tryFrom((int)$this->category);
        return $category?->label() ?? '未知';
    }
}
