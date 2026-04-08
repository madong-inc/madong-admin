<?php

return [
    /**
     * 多态关联映射配置
     * 
     * 表中存储的模型别名（key） -> 实际模型类路径（value）
     * 
     * 作用：
     * 1. 数据库表中不直接存储完整的类路径，只存储简短易读的别名
     * 2. 修改类路径时，只需更新配置，无需修改数据库
     * 3. 支持插件系统的多态关联
     * 
     * 使用示例：
     * // Model 中定义多态关联
     * public function reviewable(): \Illuminate\Database\Eloquent\Relations\MorphTo
     * {
     *     return $this->morphTo();
     * }
     * 
     * // 数据库中存储
     * reviewable_type: 'question'  // 而不是 'plugin\official\app\model\question\Question'
     * reviewable_id: 123
     * 
     * // 查询时 Laravel ORM 会自动将 'question' 映射到对应模型类
     */
    'map' => [
        // 审核模块
        'review' => \app\model\review\Review::class,

        // 会员模块
        'member' => \app\model\member\Member::class,
        'member_withdraw' => \app\model\member\MemberWithdraw::class,

        // 系统管理
        'admin' => \app\model\system\Admin::class,
        'menu' => \app\model\system\Menu::class,
    ],
];
