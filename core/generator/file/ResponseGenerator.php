<?php

namespace core\generator\file;

use core\generator\interfaces\FileGeneratorInterface;
use core\generator\utils\TemplateRenderer;

/**
 * Schema 生成器
 * 负责生成 Schema 文件内容
 */
class ResponseGenerator implements FileGeneratorInterface
{
    /**
     * @var array 配置信息
     */
    private array $config;

    /**
     * @var TemplateRenderer 模板渲染器
     */
    private TemplateRenderer $templateRenderer;

    /**
     * 构造函数
     * @param array $config 配置信息
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->templateRenderer = new TemplateRenderer();
    }

    /**
     * 生成文件内容
     * @return string 文件内容
     */
    public function generateContent(): string
    {
        $template = $this->config['template'] ?? 'app';
        $isPlugin = $template !== 'app';
        $pluginName = $this->config['namespace'] ?? '';
        
        // 生成命名空间
        $packageName = $this->config['package_name'] ?? 'default';
        if ($isPlugin) {
            // 插件模式：plugin\{plugin_name}\app\schema\response\{package_name}
            $namespace = "plugin\\{$pluginName}\\app\\schema\\response\\{$packageName}";
        } else {
            // App 模式：app\adminapi\\schema\{package_name}
            $namespace = "app\\adminapi\\schema\\response\\{$packageName}";
        }
        
        $data = [
            'class_name' => $this->config['class_name'] ?? 'DefaultModel',
            'package_name' => $this->config['package_name'] ?? 'default',
            'columns' => $this->config['columns'] ?? [],
            'namespace' => $namespace,
        ];
        
        // 生成字段注解
        $fields = '';
        if (isset($data['columns']) && is_array($data['columns'])) {
            foreach ($data['columns'] as $column) {
                // 跳过主键字段
                if (isset($column['is_pk']) && $column['is_pk']) {
                    continue;
                }
                
                // 生成字段注解
                $fields .= $this->generateFieldAnnotation($column);
            }
        }
        
        // 添加到数据中
        $data['fields'] = $fields;
        
        return $this->templateRenderer->render('server/schema/response/schema.stub', $data);
    }

    /**
     * 生成字段注解
     * @param array $column 字段信息
     * @return string 字段注解代码
     */
    private function generateFieldAnnotation(array $column): string
    {
        $columnName = $column['column_name'];
        $columnComment = $column['column_comment'];
        $isRequired = isset($column['is_required']) && $column['is_required'];
        $columnType = $column['column_type'] ?? 'string';
        
        // 确定字段类型
        $phpType = $this->getPhpType($columnType);
        $nullableType = !$isRequired ? '?' . $phpType : $phpType;
        $defaultValue = !$isRequired ? ' = null' : '';
        
        // 生成 OA\Property 注解
        $annotation = "    #[OA\Property(\n";
        $annotation .= "        description: '{$columnComment}',\n";
        $annotation .= "        type: '{$this->getOpenApiType($columnType)}',\n";
        $annotation .= "        example: '{$this->getExampleValue($columnType)}',\n";
        $annotation .= "        nullable: " . ($isRequired ? 'false' : 'true') . "\n";
        $annotation .= "    )]\n";
        
        // 生成字段声明（Schema定义不需要验证规则）
        $annotation .= "    public {$nullableType} $" . "{$columnName}{$defaultValue};\n\n";
        
        return $annotation;
    }

    /**
     * 获取 PHP 类型
     * @param string $columnType 数据库字段类型
     * @return string PHP 类型
     */
    private function getPhpType(string $columnType): string
    {
        $typeMap = [
            'int' => 'int',
            'integer' => 'int',
            'string' => 'string',
            'varchar' => 'string',
            'text' => 'string',
            'boolean' => 'bool',
            'bool' => 'bool',
            'float' => 'float',
            'double' => 'float',
            'array' => 'array',
            'json' => 'array',
        ];
        
        return $typeMap[strtolower($columnType)] ?? 'string';
    }

    /**
     * 获取 OpenAPI 类型
     * @param string $columnType 数据库字段类型
     * @return string OpenAPI 类型
     */
    private function getOpenApiType(string $columnType): string
    {
        $typeMap = [
            'int' => 'integer',
            'integer' => 'integer',
            'string' => 'string',
            'varchar' => 'string',
            'text' => 'string',
            'boolean' => 'boolean',
            'bool' => 'boolean',
            'float' => 'number',
            'double' => 'number',
            'array' => 'array',
            'json' => 'object',
        ];
        
        return $typeMap[strtolower($columnType)] ?? 'string';
    }

    /**
     * 获取示例值
     * @param string $columnType 数据库字段类型
     * @return string 示例值
     */
    private function getExampleValue(string $columnType): string
    {
        $exampleMap = [
            'int' => '1',
            'integer' => '1',
            'string' => '示例值',
            'varchar' => '示例值',
            'text' => '示例文本',
            'boolean' => 'true',
            'bool' => 'true',
            'float' => '1.0',
            'double' => '1.0',
            'array' => '[]',
            'json' => '{"key": "value"}',
        ];
        
        return $exampleMap[strtolower($columnType)] ?? '示例值';
    }

    /**
     * 生成验证规则
     * @param array $column 字段信息
     * @return string 验证规则
     */
    private function generateValidationRules(array $column): string
    {
        $columnName = $column['column_name'];
        $isRequired = isset($column['is_required']) && $column['is_required'];
        $columnType = $column['column_type'] ?? 'string';
        
        $rules = [];
        
        // 必填规则
        if ($isRequired) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        // 类型规则
        switch (strtolower($columnType)) {
            case 'int':
            case 'integer':
                $rules[] = 'integer';
                break;
            case 'float':
            case 'double':
                $rules[] = 'numeric';
                break;
            case 'boolean':
            case 'bool':
                $rules[] = 'boolean';
                break;
            case 'array':
                $rules[] = 'array';
                break;
            default:
                $rules[] = 'string';
                break;
        }
        
        return implode('|', $rules);
    }

    /**
     * 获取文件扩展名
     * @return string 文件扩展名
     */
    public function getFileExtension(): string
    {
        return 'php';
    }
}
