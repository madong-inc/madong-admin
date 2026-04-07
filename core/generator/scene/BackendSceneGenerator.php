<?php

namespace core\generator\scene;

use core\generator\interfaces\SceneGeneratorInterface;
use core\generator\utils\PathResolver;

/**
 * 后端场景生成器
 * 负责生成后端文件路径
 */
class BackendSceneGenerator implements SceneGeneratorInterface
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
     *
     * @param array $config 配置信息
     */
    public function __construct(array $config)
    {
        $this->config       = $config;
        $this->pathResolver = new PathResolver();
    }

    /**
     * 生成文件路径
     *
     * @param string $fileType  文件类型
     * @param string $extension 文件扩展名
     *
     * @return string 文件路径
     */
    public function generateFilePath(string $fileType, string $extension = 'php'): string
    {
        $template   = $this->config['template'] ?? 'app';
        $moduleName = $this->config['package_name'] ?? 'default';
        $className  = $this->config['class_name'] ?? 'DefaultModel';
        $isPlugin   = $template !== 'app';

        // 处理特殊文件类型
//        if ($fileType === 'request') {
//            // 请求需要生成两个文件：FormRequest 和 QueryRequest
//            $dtoBasePath = $this->getBasePathForFileType($fileType);
//            $formPath    = $this->pathResolver->generatePath($dtoBasePath, $moduleName, $className, 'request_form', $extension, $isPlugin);
//            $queryPath   = $this->pathResolver->generatePath($dtoBasePath, $moduleName, $className, 'request_query', $extension, $isPlugin);
//
//            // 返回表单请求路径，查询请求路径会在生成器中单独处理
//            return $formPath;
//        }

        $basePath = $this->getBasePathForFileType($fileType);
        $path     = $this->pathResolver->generatePath($basePath, $moduleName, $className, $fileType, $extension, $isPlugin);

        return $path;
    }

    /**
     * 生成文件内容
     *
     * @param string $fileType 文件类型
     *
     * @return string 文件内容
     */
    public function generateContent(string $fileType): string
    {
        // 内容生成由文件类型生成器负责
        return '';
    }

    /**
     * 获取基础路径
     *
     * @return string 基础路径
     */
    private function getBasePath(): string
    {
        $template = $this->config['template'] ?? 'app';

        if ($template === 'app') {
            return base_path() . DS . 'app';
        } else {
            $pluginName = $this->config['namespace'] ?? '';
            return base_path() . DS . 'plugin' . DS . $pluginName . DS . 'app';
        }
    }

    /**
     * 根据文件类型获取基础路径
     *
     * @param string $fileType 文件类型
     *
     * @return string 基础路径
     */
    private function getBasePathForFileType(string $fileType): string
    {
        $template = $this->config['template'] ?? 'app';

        // 插件模式下，所有文件都在插件的 app 目录下
        if ($template !== 'app') {
            $pluginName = $this->config['namespace'] ?? '';
            return base_path() . DS . 'plugin' . DS . $pluginName . DS . 'app';
        }

        // app 模式下，根据文件类型选择不同的基础路径
        $adminApiFileTypes = ['controller', 'validate', 'request_form', 'request_query','response'];
        if (in_array($fileType, $adminApiFileTypes)) {
            return base_path() . DS . 'app' . DS . 'adminapi';
        } else {
            return base_path() . DS . 'app';
        }
    }
}
