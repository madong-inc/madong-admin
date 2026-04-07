<?php

namespace core\generator;

use core\generator\factory\GeneratorFactory;
use core\generator\utils\TemplateRenderer;
use core\generator\utils\PathResolver;
use support\Log;

/**
 * 代码生成器引擎
 * 负责协调各个生成器，处理整体生成流程
 */
class GeneratorEngine
{
    /**
     * @var array 配置信息
     */
    private array $config;

    /**
     * @var GeneratorFactory 生成器工厂
     */
    private GeneratorFactory $generatorFactory;

    /**
     * @var TemplateRenderer 模板渲染器
     */
    private TemplateRenderer $templateRenderer;

    /**
     * @var PathResolver 路径解析器
     */
    private PathResolver $pathResolver;

    /**
     * 构造函数
     * @param array|object $config 配置信息（支持数组或对象）
     */
    public function __construct($config)
    {
        // 解析配置数据
        $this->config = $this->parseConfig($config);

        // 合并默认场景配置
        $this->mergeDefaultScenes();

        $this->generatorFactory = new GeneratorFactory();
        $this->templateRenderer = new TemplateRenderer();
        $this->pathResolver = new PathResolver();
    }

    /**
     * 解析配置数据
     * @param array|object $config 配置信息
     * @return array 解析后的配置数组
     */
    private function parseConfig($config): array
    {
        if (is_object($config)) {
            // 如果是对象，转换为数组
            $parsedConfig = (array) $config;
        } elseif (is_array($config)) {
            // 如果是数组，直接使用
            $parsedConfig = $config;
        } else {
            // 其他类型，返回空数组
            return [];
        }
        
        // 如果有 basic 字段，使用其中的值
        if (isset($parsedConfig['basic']) && is_array($parsedConfig['basic'])) {
            $basic = $parsedConfig['basic'];
            if (isset($basic['table_name']) && !empty($basic['table_name'])) {
                $parsedConfig['package_name'] = $basic['table_name'];
            } elseif (isset($basic['module_name']) && !empty($basic['module_name'])) {
                $parsedConfig['package_name'] = $basic['module_name'];
            }
            if (isset($basic['class_name']) && !empty($basic['class_name'])) {
                $parsedConfig['class_name'] = $basic['class_name'];
            } elseif (isset($basic['table_name']) && !empty($basic['table_name'])) {
                $parsedConfig['class_name'] = $basic['table_name'];
            }
            if (isset($basic['table_comment']) && !empty($basic['table_comment'])) {
                $parsedConfig['table_comment'] = $basic['table_comment'];
            }
            if (isset($basic['class_name_comment']) && !empty($basic['class_name_comment'])) {
                $parsedConfig['class_name_comment'] = $basic['class_name_comment'];
            }
            if (isset($basic['plugin_name']) && !empty($basic['plugin_name'])) {
                $parsedConfig['plugin_name'] = $basic['plugin_name'];
                $parsedConfig['namespace'] = $basic['plugin_name'];
            }
        }
        
        // 如果顶层有 table_name 字段，使用它作为 package_name
        if (isset($parsedConfig['table_name']) && !empty($parsedConfig['table_name']) && !isset($parsedConfig['package_name'])) {
            $parsedConfig['package_name'] = $parsedConfig['table_name'];
        } elseif (isset($parsedConfig['module_name']) && !empty($parsedConfig['module_name']) && !isset($parsedConfig['package_name'])) {
            $parsedConfig['package_name'] = $parsedConfig['module_name'];
        }
        
        // 如果顶层有 table_name 字段，使用它作为 class_name（如果没有设置 class_name）
        if (isset($parsedConfig['table_name']) && !empty($parsedConfig['table_name']) && !isset($parsedConfig['class_name'])) {
            $parsedConfig['class_name'] = $parsedConfig['table_name'];
        }
        
        // 处理 template 参数：如果没有传入 template，但传入了 plugin_name，则使用 plugin_name 作为 template 值
        if (!isset($parsedConfig['template']) && isset($parsedConfig['plugin_name']) && !empty($parsedConfig['plugin_name'])) {
            $parsedConfig['template'] = $parsedConfig['plugin_name'];
            $parsedConfig['namespace'] = $parsedConfig['plugin_name'];
        } elseif (!isset($parsedConfig['template'])) {
            // 如果都没有传入，则使用默认值 app
            $parsedConfig['template'] = 'app';
        } elseif ($parsedConfig['template'] !== 'app') {
            // 如果传入了 template 且不是 app，则使用 template 作为 namespace
            $parsedConfig['namespace'] = $parsedConfig['template'];
        }
        // 如果传入了 template，则直接使用它，优先级高于 plugin_name
        
        // 确保 class_name 是大驼峰格式
        if (isset($parsedConfig['class_name']) && !empty($parsedConfig['class_name'])) {
            $parsedConfig['class_name'] = str_replace(' ', '', ucwords(str_replace('_', ' ', $parsedConfig['class_name'])));
        }
        
        // 验证必须参数
        $this->validateRequiredParams($parsedConfig);
        
        return $parsedConfig;
    }

    
    /**
     * 验证必须参数
     * @param array $config 配置数组
     * @throws Exception 如果缺少必须参数
     */
    private function validateRequiredParams(array &$config): void
    {
        // 验证 package_name（模块名）
        if (!isset($config['package_name']) || empty($config['package_name'])) {
            throw new \Exception('Missing required parameter: package_name (table_name)');
        }
        
        // 验证 class_name（类名）
        if (!isset($config['class_name']) || empty($config['class_name'])) {
            throw new \Exception('Missing required parameter: class_name');
        }
        
        // 验证 template
        if (!isset($config['template']) || empty($config['template'])) {
            $config['template'] = 'app';
        }
    }

    /**
     * 合并默认场景配置
     * @return void
     */
    private function mergeDefaultScenes(): void
    {
        // 如果未设置场景类型，默认同时生成前端和后端代码
        if (!isset($this->config['scene_types']) && !isset($this->config['scene_type'])) {
            $this->config['scene_types'] = ['backend', 'admin'];
        }

        // 如果未设置文件类型映射，设置默认值
        if (!isset($this->config['file_types_map'])) {
            $this->config['file_types_map'] = [
                'backend' => ['controller', 'model', 'service', 'dao', 'validate', 'request_form', 'request_query', 'response'],
                'admin' => ['api', 'api_model', 'view', 'view_schema', 'lang'],
            ];
        }
    }

    /**
     * 预览代码生成
     * @return array 预览结果
     */
    public function preview(): array
    {
        $result = [];
        $sceneTypes = $this->config['scene_types'] ?? [$this->config['scene_type'] ?? 'backend'];
        $fileTypesMap = $this->config['file_types_map'] ?? [
            'backend' => ['controller', 'model', 'service', 'dao', 'validate', 'request_form', 'request_query', 'response'],
            'admin' => ['api', 'api_model', 'view', 'view_schema', 'lang'],
        ];

        // 生成每种场景类型
        foreach ($sceneTypes as $sceneType) {
            try {
                // 获取场景生成器
                $sceneGenerator = $this->generatorFactory->createSceneGenerator($sceneType, $this->config);

                // 获取当前场景的文件类型
                $fileTypes = $fileTypesMap[$sceneType] ?? $this->config['file_types'] ?? ['controller', 'model', 'service', 'dao', 'validate', 'request_form', 'request_query','response'];

                // 生成每种文件类型
                foreach ($fileTypes as $fileType) {
                    try {
                        // 获取文件类型生成器
                        $fileGenerator = $this->generatorFactory->createFileGenerator($fileType, $this->config);

                        // 生成文件内容
                        $content = $fileGenerator->generateContent();

                        // 获取文件扩展名
                        $extension = $fileGenerator->getFileExtension();

                        // 生成文件路径
                        $filePath = $sceneGenerator->generateFilePath($fileType, $extension);

                        // 添加到结果中
                        $result[] = [
                            'name' => basename($filePath),
                            'type' => $this->getFileType($filePath),
                            'content' => $content,
                            'file_dir' => $this->getRelativePath(dirname($filePath), $sceneType),
                            'scene_type' => $sceneType,
                        ];
                        
                        // 处理 DTO 特殊情况：同时预览 QueryRequest
//                        if ($fileType === 'request_query' && method_exists($fileGenerator, 'getQueryRequestContent')) {
//                            try {
//                                // 获取 QueryRequest 内容
//                                $queryContent = $fileGenerator->getQueryRequestContent();
//
//                                if (!empty($queryContent)) {
//                                    // 生成 QueryRequest 文件路径（与 FormRequest 在同一目录）
//                                    $formDir = dirname($filePath);
//                                    $fileName = basename($filePath);
//                                    $queryFileName = str_replace('FormRequest', 'QueryRequest', $fileName);
//                                    $queryFilePath = $formDir . DS . $queryFileName;
//
//                                    // 添加到结果中
//                                    $result[] = [
//                                        'name' => basename($queryFilePath),
//                                        'type' => $this->getFileType($queryFilePath),
//                                        'content' => $queryContent,
//                                        'file_dir' => $this->getRelativePath(dirname($queryFilePath), $sceneType),
//                                        'scene_type' => $sceneType,
//                                    ];
//                                }
//                            } catch (\Exception $e) {
//                                // 记录错误但继续执行
//                                $result[] = [
//                                    'name' => 'request_query',
//                                    'type' => 'error',
//                                    'content' => 'Error generating QueryRequest: ' . $e->getMessage(),
//                                    'file_dir' => '',
//                                    'scene_type' => $sceneType,
//                                ];
//                            }
//                        }
                    } catch (\Exception $e) {
                        // 记录错误但继续执行
                        $result[] = [
                            'name' => $fileType,
                            'type' => 'error',
                            'content' => 'Error: ' . $e->getMessage(),
                            'file_dir' => '',
                            'scene_type' => $sceneType,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // 记录场景错误
                $result[] = [
                    'name' => $sceneType,
                    'type' => 'error',
                    'content' => 'Scene Error: ' . $e->getMessage(),
                    'file_dir' => '',
                    'scene_type' => $sceneType,
                ];
            }
        }

        return $result;
    }

    /**
     * 部署代码生成
     * @return array 部署结果
     */
    public function deploy(): array
    {
        $result = [];
        $sceneTypes = $this->config['scene_types'] ?? [$this->config['scene_type'] ?? 'backend'];
        $fileTypesMap = $this->config['file_types_map'] ?? [
            'backend' => ['controller', 'model', 'service', 'dao', 'validate', 'response', 'request_form','request_query'],
            'admin' => ['api', 'api_model', 'view', 'view_schema', 'lang'],
        ];

        // 生成每种场景类型
        foreach ($sceneTypes as $sceneType) {
            try {
                // 获取场景生成器
                $sceneGenerator = $this->generatorFactory->createSceneGenerator($sceneType, $this->config);

                // 获取当前场景的文件类型
                $fileTypes = $fileTypesMap[$sceneType] ?? $this->config['file_types'] ?? ['controller', 'model', 'service', 'dao', 'validate', 'schema', 'dto'];

                // 生成每种文件类型
                foreach ($fileTypes as $fileType) {
                    try {
                        // 获取文件类型生成器
                        $fileGenerator = $this->generatorFactory->createFileGenerator($fileType, $this->config);

                        // 生成文件内容
                        $content = $fileGenerator->generateContent();

                        // 验证内容是否为空
                        if (empty($content)) {
                            throw new \Exception('Generated content is empty for file type: ' . $fileType);
                        }

                        // 获取文件扩展名
                        $extension = $fileGenerator->getFileExtension();

                        // 生成文件路径
                        $filePath = $sceneGenerator->generateFilePath($fileType, $extension);

                        // 确保目录存在
                        $dirPath = dirname($filePath);
                        if (!is_dir($dirPath)) {
                            mkdir($dirPath, 0777, true);
                        }

                        // 写入文件
                        $written = file_put_contents($filePath, $content);
                        if ($written === false) {
                            throw new \Exception('Failed to write file: ' . $filePath);
                        }

                        // 添加到结果中
                        $result[] = [
                            'file_type' => $fileType,
                            'file_path' => $filePath,
                            'status' => 'success',
                            'scene_type' => $sceneType,
                        ];
                        
                        // 处理 DTO 特殊情况：同时生成 QueryRequest
//                        if ($fileType === 'dto' && method_exists($fileGenerator, 'getQueryRequestContent')) {
//                            try {
//                                // 获取 QueryRequest 内容
//                                $queryContent = $fileGenerator->getQueryRequestContent();
//
//                                if (!empty($queryContent)) {
//                                    // 生成 QueryRequest 文件路径（与 FormRequest 在同一目录）
//                                    $formDir = dirname($filePath);
//                                    $fileName = basename($filePath);
//                                    $queryFileName = str_replace('FormRequest', 'QueryRequest', $fileName);
//                                    $queryFilePath = $formDir . DS . $queryFileName;
//
//                                    // 确保目录存在
//                                    if (!is_dir($formDir)) {
//                                        mkdir($formDir, 0777, true);
//                                    }
//
//                                    // 写入文件
//                                    $queryWritten = file_put_contents($queryFilePath, $queryContent);
//                                    if ($queryWritten !== false) {
//                                        $result[] = [
//                                            'file_type' => 'dto_query',
//                                            'file_path' => $queryFilePath,
//                                            'status' => 'success',
//                                            'scene_type' => $sceneType,
//                                        ];
//                                    }
//                                }
//                            } catch (\Exception $e) {
//                                // 记录错误但继续执行
//                                $result[] = [
//                                    'file_type' => 'dto_query',
//                                    'status' => 'error',
//                                    'message' => 'Error generating QueryRequest: ' . $e->getMessage(),
//                                    'scene_type' => $sceneType,
//                                ];
//                            }
//                        }
                    } catch (\Exception $e) {
                        // 记录错误但继续执行
                        $result[] = [
                            'file_type' => $fileType,
                            'status' => 'error',
                            'message' => $e->getMessage(),
                            'scene_type' => $sceneType,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // 记录场景错误
                $result[] = [
                    'file_type' => $sceneType,
                    'status' => 'error',
                    'message' => 'Scene Error: ' . $e->getMessage(),
                    'scene_type' => $sceneType,
                ];
            }
        }

        return $result;
    }

    /**
     * 获取文件类型
     * @param string $filePath 文件路径
     * @return string 文件类型
     */
    private function getFileType(string $filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return $extension ?: 'unknown';
    }

    /**
     * 获取相对路径
     * @param string $fullPath 完整路径
     * @param string $sceneType 场景类型
     * @return string 相对路径
     */
    private function getRelativePath(string $fullPath, string $sceneType): string
    {
        if ($sceneType === 'admin') {
            // 前端场景
            $template = $this->config['template'] ?? 'app';
            // 无论是 app 模式还是插件模式，前端代码都生成在后端代码根目录的兄弟节点 admin 目录下
            $frontendRoot = dirname(base_path()) . DS . 'admin';
            $path = 'admin' . str_replace($frontendRoot, '', $fullPath);
        } else {
            // 后端场景，使用当前后端根目录名称作为前缀
            $backendRootName = basename(base_path());
            $path = $backendRootName . str_replace(base_path(), '', $fullPath);
        }
        
        // 调整斜杠为 `/`
        $path = str_replace('\\', '/', $path);
        // 确保路径末尾有 `/`
        if (!empty($path) && substr($path, -1) !== '/') {
            $path .= '/';
        }
        return $path;
    }

    /**
     * 生成文件下载包
     *
     * @return array 下载包信息
     * @throws \Exception
     */
    public function download(): array
    {
        // 验证模块命名
        $this->validateModuleName($this->config);
        
        $result = [];
        $sceneTypes = $this->config['scene_types'] ?? [$this->config['scene_type'] ?? 'backend'];
        $fileTypesMap = $this->config['file_types_map'] ?? [
            'backend' => ['controller', 'model', 'service', 'dao', 'validate', 'request_form', 'request_query','response'],
            'admin' => ['api', 'api_model', 'view', 'view_schema', 'lang'],
        ];

        // 创建临时目录
        $tempDir = sys_get_temp_dir() . DS . 'generator_' . uniqid();
        if (!mkdir($tempDir, 0777, true)) {
            throw new \Exception('Failed to create temporary directory');
        }

        // 生成每种场景类型的文件
        foreach ($sceneTypes as $sceneType) {
            try {
                // 获取场景生成器
                $sceneGenerator = $this->generatorFactory->createSceneGenerator($sceneType, $this->config);

                // 获取当前场景的文件类型
                $fileTypes = $fileTypesMap[$sceneType] ?? $this->config['file_types'] ?? ['controller', 'model', 'service', 'dao', 'validate', 'schema', 'dto'];

                // 生成每种文件类型
                foreach ($fileTypes as $fileType) {
                    try {
                        // 获取文件类型生成器
                        $fileGenerator = $this->generatorFactory->createFileGenerator($fileType, $this->config);

                        // 生成文件内容
                        $content = $fileGenerator->generateContent();

                        // 验证内容是否为空
                        if (empty($content)) {
                            throw new \Exception('Generated content is empty for file type: ' . $fileType);
                        }

                        // 获取文件扩展名
                        $extension = $fileGenerator->getFileExtension();

                        // 生成文件路径
                        $filePath = $sceneGenerator->generateFilePath($fileType, $extension);

                        // 构建临时文件路径
                        $relativePath = str_replace(dirname(base_path()), '', $filePath);
                        $relativePath = ltrim($relativePath, DS);
                        $tempFilePath = $tempDir . DS . $relativePath;

                        // 确保目录存在
                        $tempDirPath = dirname($tempFilePath);
                        if (!is_dir($tempDirPath)) {
                            mkdir($tempDirPath, 0777, true);
                        }

                        // 写入临时文件
                        if (file_put_contents($tempFilePath, $content) === false) {
                            throw new \Exception('Failed to write temporary file: ' . $tempFilePath);
                        }

                        // 处理 DTO 特殊情况：同时生成 QueryRequest
                        if ($fileType === 'dto' && method_exists($fileGenerator, 'getQueryRequestContent')) {
                            try {
                                // 获取 QueryRequest 内容
                                $queryContent = $fileGenerator->getQueryRequestContent();
                                
                                if (!empty($queryContent)) {
                                    // 生成 QueryRequest 文件路径
                                    $formDir = dirname($filePath);
                                    $fileName = basename($filePath);
                                    $queryFileName = str_replace('FormRequest', 'QueryRequest', $fileName);
                                    $queryFilePath = $formDir . DS . $queryFileName;
                                    
                                    // 构建临时文件路径（与 FormRequest 处理方式一致）
                                    $queryRelativePath = str_replace(dirname(base_path()), '', $queryFilePath);
                                    $queryRelativePath = ltrim($queryRelativePath, DS);
                                    $tempQueryFilePath = $tempDir . DS . $queryRelativePath;
                                    
                                    // 确保目录存在
                                    $tempQueryDir = dirname($tempQueryFilePath);
                                    if (!is_dir($tempQueryDir)) {
                                        mkdir($tempQueryDir, 0777, true);
                                    }
                                    
                                    // 写入临时文件
                                    if (file_put_contents($tempQueryFilePath, $queryContent) === false) {
                                        throw new \Exception('Failed to write temporary QueryRequest file: ' . $tempQueryFilePath);
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::error('Error generating QueryRequest: ' . $e->getMessage(), [
                                    'file_type' => $fileType,
                                    'trace' => $e->getTraceAsString()
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error generating file: ' . $e->getMessage(), [
                            'file_type' => $fileType,
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error in scene generation: ' . $e->getMessage(), [
                    'scene_type' => $sceneType,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // 创建 zip 文件
        $zipFileName = 'generator_' . uniqid() . '.zip';
        $zipFilePath = sys_get_temp_dir() . DS . $zipFileName;
        
        if ($this->createZip($tempDir, $zipFilePath)) {
            // 清理临时目录
            $this->cleanupDir($tempDir);
            
            // 确保返回的路径是绝对路径且格式正确
            $zipFilePath = realpath($zipFilePath);
            if (!$zipFilePath) {
                throw new \Exception('Failed to get real path for zip file');
            }
            
            // 检查zip文件大小，确保不是空文件
            $fileSize = filesize($zipFilePath);
            if ($fileSize < 22) { // 空zip文件的最小大小约为22字节
                throw new \Exception('Generated zip file is empty');
            }
            
            // 确保路径使用Windows兼容的反斜杠格式
            $zipFilePath = str_replace('/', '\\', $zipFilePath);
            
            return [
                'status' => 'success',
                'file_path' => $zipFilePath,
                'file_name' => $zipFileName,
                'file_size' => $fileSize,
            ];
        } else {
            // 清理临时目录
            $this->cleanupDir($tempDir);
            
            throw new \Exception('Failed to create zip file');
        }
    }

    /**
     * 创建 zip 文件
     * @param string $sourceDir 源目录
     * @param string $zipFilePath zip 文件路径
     * @return bool 是否成功
     */
    private function createZip(string $sourceDir, string $zipFilePath): bool
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        return $zip->close();
    }

    /**
     * 清理目录
     * @param string $dir 目录路径
     */
    private function cleanupDir(string $dir): void
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . DS . $file;
                if (is_dir($path)) {
                    $this->cleanupDir($path);
                } else {
                    unlink($path);
                }
            }
            rmdir($dir);
        }
    }

    /**
     * 验证模块命名
     * @param array $config 生成配置
     * @throws \Exception 验证失败时抛出异常
     */
    private function validateModuleName(array $config): void
    {
        // 直接读取配置文件
        $configFile = __DIR__ . '/config/app.php';
        $restrictions = file_exists($configFile) ? include $configFile : [];
        // 获取模块命名限制配置
        $restrictions = $restrictions['module_name_restrictions'] ?? [];
        // 如果未启用验证，直接返回
        if (!isset($restrictions['enabled']) || !$restrictions['enabled']) {
            return;
        }
        
        // 获取模块名
        $moduleName = $config['package_name'] ?? null;
        
        if (!$moduleName) {
            return;
        }
        
        // 检查是否为保留名称
        if (isset($restrictions['reserved_names']) && is_array($restrictions['reserved_names'])) {
            if (in_array(strtolower($moduleName), array_map('strtolower', $restrictions['reserved_names']))) {
                throw new \Exception(sprintf('Module name "%s" is reserved and cannot be used', $moduleName));
            }
        }
        
        // 检查命名格式
        if (isset($restrictions['pattern']) && $restrictions['pattern']) {
            if (!preg_match($restrictions['pattern'], $moduleName)) {
                throw new \Exception(sprintf('Module name "%s" is invalid. It should match pattern: %s', $moduleName, $restrictions['pattern']));
            }
        }
    }
}
