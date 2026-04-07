<?php
declare(strict_types=1);

namespace app\model\member;

use core\base\BaseModel;


/**
 * 会员等级模型
 */
class MemberLevel extends BaseModel
{
    /**
     * 数据表名称
     */
    protected $table = 'member_level';

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
        'level',
        'min_points',
        'max_points',
        'discount',
        'icon',
        'color',
        'description',
        'enabled',
        'created_at',
        'updated_at',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
    ];


    /**
     * 关联会员
     */
    public function members(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany|MemberLevel
    {
        return $this->hasMany(Member::class, 'level_id', 'id');
    }
}