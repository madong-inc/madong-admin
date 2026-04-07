<?php

namespace core\generator\file;

use core\generator\interfaces\FileGeneratorInterface;
use core\generator\utils\TemplateRenderer;

/**
 * 语言包生成器
 * 负责生成前端语言包文件内容
 */
class LangGenerator implements FileGeneratorInterface
{
    private array $config;
    private TemplateRenderer $templateRenderer;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->templateRenderer = new TemplateRenderer();
    }

    public function generateContent(): string
    {
        $className = $this->config['class_name'] ?? 'DefaultModel';
        $moduleName = $this->config['package_name'] ?? 'default';
        $columns = $this->config['columns'] ?? [];

        $langKey = $this->generateLangKey($moduleName, $className);
        $fields = $this->generateFieldsTranslations($columns, $langKey);
        $classComment = $this->getClassComment($columns);

        $content = $this->templateRenderer->render('admin/lang/zh-cn/index.stub', [
            'module_name' => $moduleName,
            'class_name' => $className,
            'lang_key' => $langKey,
            'class_name_comment' => $classComment,
            'fields' => $fields,
        ]);

        return $content;
    }

    private function generateLangKey(string $moduleName, string $className): string
    {
        $classKey = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $className));
        return "{$classKey}";
    }

    private function generateFieldsTranslations(array $columns, string $langKey): string
    {
        $listFields = [];
        $formFields = [];
        $searchFields = [];
        
        foreach ($columns as $column) {
            $fieldName = $column['column_name'];
            $comment = $column['column_comment'] ?? $fieldName;
            
            if (isset($column['is_lists']) && $column['is_lists'] == 1) {
                $listFields[] = "      \"{$fieldName}\": \"{$comment}\"";
            }
            
            if ((isset($column['is_insert']) && $column['is_insert'] == 1) || 
                (isset($column['is_update']) && $column['is_update'] == 1)) {
                $formFields[] = "      \"{$fieldName}\": \"{$comment}\"";
            }
            
            if (isset($column['is_search']) && $column['is_search'] == 1) {
                $searchFields[] = "      \"{$fieldName}\": \"{$comment}\"";
            }
        }
        
        $result = '';
        
        if (!empty($listFields)) {
            $result .= "    \"list\": {\n" . implode(",\n", $listFields) . "\n    },\n";
        }
        
        if (!empty($formFields)) {
            $result .= "    \"form\": {\n" . implode(",\n", $formFields) . "\n    },\n";
        }
        
        if (!empty($searchFields)) {
            $result .= "    \"search\": {\n" . implode(",\n", $searchFields) . "\n    }";
        }
        
        return rtrim($result, ",\n");
    }

    private function getClassComment(array $columns): string
    {
        return $this->config['class_name_comment'] ?? $this->config['table_comment'] ?? $this->config['class_name'];
    }

    public function getFileExtension(): string
    {
        return 'json';
    }
}
