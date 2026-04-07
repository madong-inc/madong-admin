<?php
declare(strict_types=1);
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\model\generator;

use core\base\BaseModel;

/**
 * 代码生成器-表
 *
 * @author Mr.April
 * @since  1.0
 */
class GeneratorTable extends BaseModel
{

    protected $table = 'generate_table';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'table_name',
        'table_content',
        'module_name',
        'class_name',
        'edit_type',
        'plugin_name',
        'order_type',
        'parent_menu',
        'relations',
        'push_sync_count',
        'created_at',
        'updated_at',
    ];

    /**
     * 关联字段
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany|\app\model\generator\GeneratorTable
     */
    public function columns(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany|GeneratorTable
    {
        return $this->hasMany(GeneratorColumn::class, 'table_id', 'id');
    }

}
