<?php
/**
 * 示例插件 - 审核配置示例
 * 
 * 此文件展示如何为插件配置审核功能
 * 系统会自动扫描并合并此配置
 */

return [
    // 插件信息
    'plugin' => [
        'name' => 'example',
        'display_name' => '示例插件',
        'version' => '1.0.0',
    ],
    
    // ==================== 审核类型配置示例 ====================
    
    // 示例1：简单配置（仅使用默认映射）
    'example_simple' => [
        'morph_alias' => 'example_simple',      // 对应 morph_map.php 中的别名
        'display_name' => '简单示例审核',
        'priority' => 5,
        
        // 字段映射（如果为空，使用系统默认映射）
        'fields' => [
            'title' => [
                'type' => 'attribute',
                'source' => 'title',           // 模型中的字段名
                'label' => '标题',
                'fallback' => '默认标题',
            ],
            'content' => [
                'type' => 'attribute',
                'source' => 'description',     // 可能字段名不同
                'label' => '内容',
                'fallback' => '无内容',
            ],
        ],
    ],
    
    // 示例2：复杂配置（使用关联和回调）
    'example_complex' => [
        'morph_alias' => 'example_complex',
        'display_name' => '复杂示例审核',
        'priority' => 10,
        
        'fields' => [
            'title' => [
                'type' => 'callback',
                'callback' => function($model) {
                    // 自定义标题生成逻辑
                    return $model->name . ' (ID: ' . $model->id . ')';
                },
                'label' => '标题',
                'fallback' => '未命名',
            ],
            'content' => [
                'type' => 'attribute',
                'source' => 'description',
                'label' => '描述',
                'format' => 'html_to_text',    // 应用格式化函数
                'fallback' => '无描述',
            ],
            'applicant' => [
                'type' => 'relation',
                'source' => 'creator',          // 关联关系名
                'attribute' => 'username',      // 关联模型的字段
                'label' => '创建人',
                'fallback' => '未知用户',
            ],
            'category' => [
                'type' => 'relation',
                'source' => 'category',         // 关联关系
                'attribute' => 'name',          // 关联模型的字段
                'label' => '分类',
                'fallback' => '未分类',
            ],
            'tags' => [
                'type' => 'relation',
                'source' => 'tags',             // 多对多关联
                'attribute' => 'name',          // 关联模型的字段
                'label' => '标签',
                'multiple' => true,             // 多值字段
                'format' => 'implode:、',       // 多个值用顿号连接
                'fallback' => '无标签',
            ],
        ],
        
        // 审核回调（可选）
        'callbacks' => [
            'approved' => 'plugin\example\app\service\ExampleReviewService@onApproved',
            'rejected' => 'plugin\example\app\service\ExampleReviewService@onRejected',
        ],
        
        // 额外信息（用于前端显示）
        'extra' => [
            'icon' => 'ant-design:experiment-outlined',
            'color' => '#722ed1',
            'route' => '/example/detail/{id}',  // 详情页路由
        ],
    ],
    
    // ==================== 字段格式化函数示例 ====================
    
    'formatters' => [
        'my_custom_formatter' => function($value) {
            // 自定义格式化函数
            return strtoupper($value);
        },
        'currency' => function($amount, $currency = '¥') {
            // 货币格式化
            return $currency . number_format($amount, 2);
        },
        'short_date' => function($timestamp) {
            // 短日期格式
            return date('Y-m-d', $timestamp);
        },
    ],
    
    // ==================== 插件级默认字段 ====================
    
    'default_fields' => [
        'created_time' => [
            'type' => 'attribute',
            'source' => 'created_at',
            'label' => '创建时间',
            'format' => 'datetime:Y年m月d日 H:i:s',
        ],
        'status_text' => [
            'type' => 'callback',
            'callback' => function($model) {
                $statusMap = [0 => '待处理', 1 => '进行中', 2 => '已完成'];
                return $statusMap[$model->status] ?? '未知';
            },
            'label' => '状态',
        ],
    ],
    
    // ==================== 使用说明 ====================
    
    '_notes' => [
        '1. 确保在 morph_map.php 中配置了模型别名映射',
        '2. priority 数字越大，排序越靠前',
        '3. 字段映射优先级：插件特定配置 > 插件默认配置 > 系统默认配置',
        '4. 可以自定义格式化函数，然后在 format 中使用',
    ],
];