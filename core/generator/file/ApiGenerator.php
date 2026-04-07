<?php

namespace core\generator\file;

use core\generator\interfaces\FileGeneratorInterface;
use core\generator\utils\TemplateRenderer;

/**
 * API 生成器
 * 负责生成前端 API 文件内容
 */
class ApiGenerator implements FileGeneratorInterface
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
        $className = $this->config['class_name'] ?? 'DefaultModel';
        $moduleName = $this->config['package_name'] ?? 'default';
        
        // 生成带类名的接口类型名称
        $rowTypeName = $className . 'Row';
        
        // 生成baseUrl，使用连字符（-）而不是下划线（_）
        $basePath = strtolower(str_replace('_', '-', $moduleName));
        $classPath = strtolower(str_replace('_', '-', $className));
        $baseUrl = $this->config['base_url'] ?? '/' . $basePath . '/' . $classPath;
        
        $content = $this->templateRenderer->render('admin/api/index.stub', [
            'class_name' => $className,
            'module_name' => $moduleName,
            'base_url' => $baseUrl,
            'row_type_name' => $rowTypeName,
        ]);
        
        return $content;
    }

    /**
     * 获取文件扩展名
     * @return string 文件扩展名
     */
    public function getFileExtension(): string
    {
        return 'ts';
    }
}