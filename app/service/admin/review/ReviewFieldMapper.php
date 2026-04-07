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
 * Official Website: http://www.madong.tech
 */

namespace app\service\admin\review;

use app\model\review\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * 审核字段映射服务
 * 
 * 功能：
 * 1. 自动扫描并合并系统和所有插件的审核配置
 * 2. 根据审核类型动态映射字段（title/content/applicant）
 * 3. 支持多种字段映射方式（属性/关联/回调）
 * 4. 插件无需修改系统代码即可扩展审核类型
 */
class ReviewFieldMapper
{
    /**
     * @var array 缓存的配置（系统 + 所有插件）
     */
    protected static array $configCache = [];

    /**
     * @var array 缓存的字段映射
     */
    protected static array $fieldMappings = [];

    /**
     * @var array 缓存的格式化函数
     */
    protected static array $formatters = [];

    /**
     * 初始化配置（懒加载）
     */
    protected static function init(): void
    {
        if (!empty(self::$configCache)) {
            return;
        }

        // 1. 加载系统主配置
        $config = config('review', []);
        
        // 2. 扫描并合并插件配置
        $pluginConfigs = self::scanPluginConfigs($config['scan_plugins'] ?? true);
        
        // 3. 合并配置
        self::$configCache = self::mergeConfigs($config, $pluginConfigs);
        
        // 4. 构建字段映射索引
        self::buildFieldMappings();
        
        // 5. 初始化格式化函数
        self::initFormatters();
    }

    /**
     * 扫描插件配置文件
     */
    protected static function scanPluginConfigs(bool $enabled): array
    {
        $pluginConfigs = [];
        
        if (!$enabled) {
            return $pluginConfigs;
        }

        $ignoredPlugins = config('review.ignored_plugins', []);
        $pluginConfigFile = config('review.plugin_config_file', 'review.php');
        $pluginPath = base_path() . '/plugin';

        if (!is_dir($pluginPath)) {
            return $pluginConfigs;
        }

        $plugins = scandir($pluginPath);
        foreach ($plugins as $plugin) {
            if ($plugin === '.' || $plugin === '..') {
                continue;
            }
            
            // 检查是否在忽略列表中
            if (in_array($plugin, $ignoredPlugins, true)) {
                continue;
            }
            
            $configFile = "{$pluginPath}/{$plugin}/config/{$pluginConfigFile}";
            if (!is_file($configFile)) {
                continue;
            }
            
            $config = include $configFile;
            if (is_array($config)) {
                // 为插件配置添加标识
                $config['_plugin'] = [
                    'name' => $plugin,
                    'display_name' => $config['plugin']['display_name'] ?? $plugin,
                ];
                
                // 按插件名存储，避免冲突
                $pluginConfigs[$plugin] = $config;
            }
        }

        return $pluginConfigs;
    }

    /**
     * 合并系统配置和插件配置
     */
    protected static function mergeConfigs(array $systemConfig, array $pluginConfigs): array
    {
        $merged = $systemConfig;
        
        // 初始化合并结构
        if (!isset($merged['types'])) {
            $merged['types'] = [];
        }
        
        if (!isset($merged['field_mappings'])) {
            $merged['field_mappings'] = [];
        }
        
        if (!isset($merged['default_field_mappings'])) {
            $merged['default_field_mappings'] = [];
        }

        // 合并插件配置
        foreach ($pluginConfigs as $pluginName => $pluginConfig) {
            // 合并审核类型
            if (isset($pluginConfig['types'])) {
                foreach ($pluginConfig['types'] as $typeKey => $typeConfig) {
                    $merged['types'][$typeKey] = $typeConfig;
                }
            }
            
            // 合并字段映射
            if (isset($pluginConfig['field_mappings'])) {
                foreach ($pluginConfig['field_mappings'] as $typeKey => $mappings) {
                    $merged['field_mappings'][$typeKey] = $mappings;
                }
            }
            
            // 合并格式化函数
            if (isset($pluginConfig['formatters'])) {
                if (!isset($merged['formatters'])) {
                    $merged['formatters'] = [];
                }
                $merged['formatters'] = array_merge($merged['formatters'], $pluginConfig['formatters']);
            }
        }

        return $merged;
    }

    /**
     * 构建字段映射索引
     */
    protected static function buildFieldMappings(): void
    {
        self::$fieldMappings = [];
        $config = self::$configCache;

        // 1. 处理所有审核类型
        foreach ($config['field_mappings'] ?? [] as $typeKey => $mapping) {
            if (is_string($mapping) && class_exists($mapping)) {
                // 简单映射：类型 => 模型类
                self::$fieldMappings[$typeKey] = [
                    'model' => $mapping,
                    'fields' => [],
                ];
            } elseif (is_array($mapping)) {
                // 详细映射配置
                self::$fieldMappings[$typeKey] = $mapping;
            }
        }

        // 2. 获取 morph_map 映射（用于通过别名查找模型）
        $morphMap = config('morph_map.map', []);
        
        // 3. 关联 morph_map 和 field_mappings
        foreach ($morphMap as $alias => $modelClass) {
            // 如果 field_mappings 中有此模型的配置，建立关联
            foreach (self::$fieldMappings as $typeKey => $mapping) {
                $mappingModel = $mapping['model'] ?? null;
                if ($mappingModel && $mappingModel === $modelClass) {
                    // 建立别名到配置的映射
                    self::$fieldMappings['_by_morph'][$alias] = $typeKey;
                    break;
                }
            }
        }
    }

    /**
     * 初始化格式化函数
     */
    protected static function initFormatters(): void
    {
        $config = self::$configCache;
        self::$formatters = $config['formatters'] ?? [];

        // 内置格式化函数
        $builtinFormatters = [
            'trim' => fn($value) => trim($value ?? ''),
            'html_to_text' => fn($html) => strip_tags($html ?? ''),
            'implode' => function($value, $glue = ',') {
                if (is_array($value)) {
                    return implode($glue, $value);
                }
                return $value;
            },
            'datetime' => function($timestamp, $format = 'Y-m-d H:i:s') {
                if (is_numeric($timestamp)) {
                    return date($format, (int)$timestamp);
                }
                return $timestamp;
            },
            'limit' => function($text, $length = 50) {
                return Str::limit($text, $length);
            },
        ];

        self::$formatters = array_merge($builtinFormatters, self::$formatters);
    }

    /**
     * 格式化字段值
     */
    protected static function formatValue($value, ?array $fieldConfig): mixed
    {
        if ($value === null || empty($fieldConfig)) {
            return $value;
        }

        // 应用格式化函数
        $format = $fieldConfig['format'] ?? null;
        if ($format) {
            // 处理带参数的格式化函数，如 "implode:、"
            if (is_string($format) && str_contains($format, ':')) {
                [$func, $param] = explode(':', $format, 2);
                $formatter = self::$formatters[$func] ?? null;
                if ($formatter) {
                    return $formatter($value, $param);
                }
            } elseif (isset(self::$formatters[$format])) {
                return self::$formatters[$format]($value);
            }
        }

        return $value;
    }

    /**
     * 根据 morph_map 别名获取字段配置
     */
    protected static function getFieldConfigByMorphAlias(string $morphAlias): ?array
    {
        $config = self::$fieldMappings['_by_morph'][$morphAlias] ?? null;
        if (!$config) {
            return null;
        }
        
        return self::$fieldMappings[$config] ?? null;
    }

    /**
     * 根据审核记录获取字段映射配置
     */
    protected static function getFieldConfig(Review $review): ?array
    {
        $reviewableType = $review->reviewable_type;
        
        // 1. 首先尝试通过 morph_map 别名查找
        $morphMap = config('morph_map.map', []);
        $morphAlias = array_search($reviewableType, $morphMap, true);
        
        if ($morphAlias !== false) {
            $config = self::getFieldConfigByMorphAlias($morphAlias);
            if ($config) {
                return $config;
            }
        }
        
        // 2. 直接通过 reviewable_type（完整类名）查找
        return self::$fieldMappings[$reviewableType] ?? null;
    }

    /**
     * 获取单个字段的值
     */
    protected static function getFieldValue($model, string $fieldName, array $fieldConfig): mixed
    {
        if (!$model || empty($fieldConfig)) {
            return $fieldConfig['fallback'] ?? null;
        }

        $type = $fieldConfig['type'] ?? 'attribute';
        $source = $fieldConfig['source'] ?? $fieldName;
        
        switch ($type) {
            case 'attribute':
                // 直接属性
                $value = $model->{$source} ?? null;
                break;
                
            case 'relation':
                // 通过关联获取
                if (method_exists($model, $source)) {
                    $relation = $model->{$source};
                    if ($relation) {
                        $attribute = $fieldConfig['attribute'] ?? 'name';
                        $value = $relation->{$attribute} ?? null;
                    } else {
                        $value = null;
                    }
                } else {
                    $value = null;
                }
                break;
                
            case 'callback':
                // 回调函数
                $callback = $fieldConfig['callback'] ?? null;
                if (is_callable($callback)) {
                    $value = $callback($model);
                } else {
                    $value = null;
                }
                break;
                
            case 'fixed':
                // 固定值
                $value = $fieldConfig['value'] ?? null;
                break;
                
            default:
                $value = null;
        }

        // 应用默认值
        if ($value === null || $value === '') {
            $value = $fieldConfig['fallback'] ?? null;
        }

        // 格式化
        return self::formatValue($value, $fieldConfig);
    }

    /**
     * 映射审核记录字段
     * 
     * @param Review $review 审核记录
     * @param array $extraFields 额外需要映射的字段
     * @return array 映射后的字段数组
     */
    public static function mapReview(Review $review, array $extraFields = []): array
    {
        self::init();
        
        $reviewable = $review->reviewable;
        $fieldConfig = self::getFieldConfig($review);
        
        // 如果找不到配置，返回基本信息
        if (!$fieldConfig || !$reviewable) {
            return self::getFallbackFields($review);
        }

        $fields = [];
        
        // 1. 映射标准字段
        $standardFields = ['title', 'content', 'applicant'];
        foreach ($standardFields as $field) {
            $fieldConfigItem = $fieldConfig['fields'][$field] ?? self::$configCache['default_field_mappings'][$field] ?? null;
            $fields[$field] = self::getFieldValue($reviewable, $field, $fieldConfigItem);
        }
        
        // 2. 映射额外字段
        foreach ($extraFields as $field) {
            if (isset($fieldConfig['fields'][$field])) {
                $fields[$field] = self::getFieldValue($reviewable, $field, $fieldConfig['fields'][$field]);
            }
        }
        
        // 3. 添加审核基础信息
        $fields = array_merge($fields, self::getBaseReviewFields($review, $fieldConfig));
        
        return $fields;
    }

    /**
     * 批量映射审核记录
     */
    public static function mapReviews(Collection $reviews, array $extraFields = []): Collection
    {
        return $reviews->map(function (Review $review) use ($extraFields) {
            $mapped = self::mapReview($review, $extraFields);
            
            // 将映射字段合并到审核记录中
            foreach ($mapped as $key => $value) {
                if (!isset($review->{$key})) {
                    $review->{$key} = $value;
                }
            }
            
            return $review;
        });
    }

    /**
     * 获取审核基础字段（无需配置）
     */
    protected static function getBaseReviewFields(Review $review, ?array $fieldConfig = null): array
    {
        return [
            'id' => $review->id,
            'reviewable_type' => $review->reviewable_type,
            'reviewable_id' => $review->reviewable_id,
            'status' => $review->status,
            'status_text' => $review->status_text,
            'reason' => $review->reason,
            'reviewer_id' => $review->reviewer_id,
            'reviewer_name' => $review->reviewer?->real_name,
            'reviewed_at' => $review->reviewed_at,
            'created_at' => $review->created_at,
            'updated_at' => $review->updated_at,
            'display_name' => $fieldConfig['display_name'] ?? $review->reviewable_type,
            'morph_alias' => self::getMorphAlias($review->reviewable_type),
        ];
    }

    /**
     * 获取 fallback 字段（当找不到配置时）
     */
    protected static function getFallbackFields(Review $review): array
    {
        $reviewable = $review->reviewable;
        
        return array_merge(
            self::getBaseReviewFields($review),
            [
                'title' => $reviewable ? '已删除的数据' : '数据不存在',
                'content' => '',
                'applicant' => '未知',
            ]
        );
    }

    /**
     * 通过类名获取 morph_map 别名
     */
    protected static function getMorphAlias(string $className): ?string
    {
        $morphMap = config('morph_map.map', []);
        return array_search($className, $morphMap, true) ?: null;
    }

    /**
     * 获取所有已配置的审核类型
     */
    public static function getConfiguredTypes(): array
    {
        self::init();
        
        $types = [];
        foreach (self::$fieldMappings as $key => $config) {
            if ($key === '_by_morph') continue;
            
            $types[$key] = [
                'display_name' => $config['display_name'] ?? $key,
                'model' => $config['model'] ?? null,
                'morph_alias' => self::getMorphAlias($config['model'] ?? ''),
                'priority' => $config['priority'] ?? 0,
                'icon' => $config['extra']['icon'] ?? null,
                'color' => $config['extra']['color'] ?? null,
            ];
        }
        
        // 按优先级排序
        uasort($types, fn($a, $b) => ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0));
        
        return $types;
    }

    /**
     * 获取指定类型的字段配置
     */
    public static function getTypeConfig(string $typeKey): ?array
    {
        self::init();
        return self::$fieldMappings[$typeKey] ?? null;
    }
}