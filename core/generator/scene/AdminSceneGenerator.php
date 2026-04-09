<?php

namespace core\generator\scene;

use core\generator\interfaces\SceneGeneratorInterface;
use core\generator\utils\PathResolver;

/**
 * 前端场景生成器
 * 负责生成前端文件路径
 */
class AdminSceneGenerator implements SceneGeneratorInterface
{
    /**
     * @var array 配置信息
     */
    private array $config;

    /**
     * @var PathResolver 路径解析器
     */
    private PathResolver $pathResolver;

    /**
     * 构造函数
     * @param array $config 配置信息
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pathResolver = new PathResolver();
    }

    /**
     * 生成文件路径
     * @param string $fileType 文件类型
     * @param string $extension 文件扩展名
     * @return string 文件路径
     */
    public function generateFilePath(string $fileType, string $extension = 'php'): string
    {
        $template = $this->config['template'] ?? 'app';
        $moduleName = $this->config['package_name'] ?? 'default';
        $className = $this->config['class_name'] ?? 'DefaultModel';
        
        // 根据模板类型选择不同的基础路径和路径生成逻辑
        if ($template === 'app') {
            $basePath = $this->getBasePath() . DS . 'src';
            $path = $this->pathResolver->generatePath($basePath, $moduleName, $className, $fileType, $extension);
        } else {
            $pluginName = $this->config['namespace'] ?? '';
            $basePath = $this->getBasePath() . DS . 'src' . DS . 'apps' . DS . $pluginName;
            $path = $this->pathResolver->generatePath($basePath, $moduleName, $className, $fileType, $extension);
        }
        
        return $path;
    }

    /**
     * 生成文件内容
     * @param string $fileType 文件类型
     * @return string 文件内容
     */
    public function generateContent(string $fileType): string
    {
        // 内容生成由文件类型生成器负责
        return '';
    }

    /**
     * 获取基础路径
     * @return string 基础路径
     */
    private function getBasePath(): string
    {
        $template = $this->config['template'] ?? 'app';
        
        // 无论是 app 模式还是插件模式，前端代码都统一生成在 frontend 目录下的 admin 目录中
        // 前端目录结构：project_root/frontend/admin 或 project_root/frontend/web 等
        return dirname(base_path()) . DS . 'frontend' . DS . 'admin';
    }
}