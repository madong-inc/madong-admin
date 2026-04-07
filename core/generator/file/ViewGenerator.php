<?php

namespace core\generator\file;

use core\generator\interfaces\FileGeneratorInterface;
use core\generator\utils\TemplateRenderer;

/**
 * 视图生成器
 * 负责生成前端视图文件内容
 */
class ViewGenerator implements FileGeneratorInterface
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
        $dummyClassName = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $className));
        $serviceName = $className . 'Service';
        $kebabModuleName = str_replace('_', '-', $moduleName);
        $kebabClassName = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $className));
        $template = $this->config['template'] ?? 'app';
        $isPlugin = $template !== 'app';
        $pluginName = $this->config['namespace'] ?? '';

        // 生成 API import 路径
        if ($isPlugin) {
            // 插件模式: @/apps/{plugin}/api/{kebab_module_name}
            $apiImportPath = '@/apps/' . $pluginName . '/api/' . $kebabModuleName;
        } else {
            // 应用模式: @/api/{kebab_module_name}
            $apiImportPath = '@/api/' . $kebabModuleName;
        }

        $langKey = $this->generateLangKey($moduleName, $className);
        $langImportPath = $this->generateLangImportPath($moduleName);

        $content = $this->templateRenderer->render('admin/views/index.stub', [
            'module_name' => $moduleName,
            'class_name' => $className,
            'dummy_class_name' => $dummyClassName,
            'service_name' => $serviceName,
            'kebab_module_name' => $kebabModuleName,
            'kebab_class_name' => $kebabClassName,
            'api_import_path' => $apiImportPath,
            'lang_key' => $langKey,
            'lang_import_path' => $langImportPath,
        ]);

        return $content;
    }

    private function generateLangKey(string $moduleName, string $className): string
    {
        $moduleKey = strtolower(str_replace('-', '_', $moduleName));
        $classKey = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $className));
        return "{$moduleKey}_{$classKey}";
    }

    private function generateLangImportPath(string $moduleName): string
    {
        return "@/lang/{$moduleName}";
    }

    public function getFileExtension(): string
    {
        return 'vue';
    }
}
