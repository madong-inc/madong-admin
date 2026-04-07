<?php

namespace core\generator\file;

use core\generator\interfaces\FileGeneratorInterface;
use core\generator\utils\TemplateRenderer;

/**
 * 控制器生成器
 * 负责生成控制器文件内容
 */
class ControllerGenerator implements FileGeneratorInterface
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
     *
     * @param array $config 配置信息
     */
    public function __construct(array $config)
    {
        $this->config           = $config;
        $this->templateRenderer = new TemplateRenderer();
    }

    /**
     * 生成文件内容
     *
     * @return string 文件内容
     */
    public function generateContent(): string
    {
        $template    = $this->config['template'] ?? 'app';
        $isPlugin    = $template !== 'app';
        $pluginName  = $this->config['namespace'] ?? '';
        $className   = $this->config['class_name'] ?? 'DefaultModel';
        $packageName = $this->config['package_name'] ?? 'default';

        // 生成连字符格式的名称（用于路由path）
        $packageNameDash = str_replace('_', '-', $packageName);
        $classNameDash   = str_replace('_', '-', strtolower($className));

        // 生成小写下划线格式的名称（用于权限码）
        $packageNameLower = strtolower($packageName);
        $classNameLower   = strtolower(str_replace('_', '', $className));

        // 生成命名空间
        if ($isPlugin) {
            // 插件模式：plugin\{plugin_name}\app\controller\{package_name}
            $namespace               = "plugin\\{$pluginName}\\app\\controller\\{$packageName}";
            $schemaRequestNamespace  = "plugin\\{$pluginName}\\app\\schema\\request\\{$packageName}";
            $validateNamespace       = "plugin\\{$pluginName}\\app\\validate\\{$packageName}";
            $schemaResponseNamespace = "plugin\\{$pluginName}\\app\\schema\\response\\{$packageName}";
            $serviceNamespace        = "plugin\\{$pluginName}\\app\\service\\{$packageName}";
        } else {
            // App 模式
            $namespace               = "app\\adminapi\\controller\\{$packageName}";
            $schemaRequestNamespace  = "app\\adminapi\\schema\\request\\{$packageName}";
            $validateNamespace       = "app\\adminapi\\validate\\{$packageName}";
            $schemaResponseNamespace = "app\\adminapi\\schema\\response\\{$packageName}";
            $serviceNamespace        = "app\\service\\admin\\{$packageName}";
        }

        $data = [
            'class_name'                => $className,
            'package_name'              => $packageName,
            'package_name_dash'         => $packageNameDash,
            'class_name_dash'           => $classNameDash,
            'package_name_lower'        => $packageNameLower,
            'class_name_lower'          => $classNameLower,
            'table_content'             => $this->config['table_content'] ?? '默认模型',
            'camel_class_name'          => lcfirst($className),
            'namespace'                 => $namespace,
            'schema_request_namespace'  => $schemaRequestNamespace,
            'validate_namespace'        => $validateNamespace,
            'schema_response_namespace' => $schemaResponseNamespace,
            'service_namespace'         => $serviceNamespace,
        ];

        return $this->templateRenderer->render('server/controller/controller.stub', $data);
    }

    /**
     * 获取文件扩展名
     *
     * @return string 文件扩展名
     */
    public function getFileExtension(): string
    {
        return 'php';
    }
}
