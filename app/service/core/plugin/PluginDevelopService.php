<?php
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

namespace app\service\core\plugin;

use app\enum\plugin\FrontendType;

/**
 * Plugin开发服务层
 *
 * @author Mr.April
 * @since  1.0
 */
class PluginDevelopService extends PluginBaseService
{
    /**
     * 模板文件路径映射
     */
    private const STUB_PATHS = [
        'api'            => '/stubs/admin/api/index.stub',
        'view'           => '/stubs/admin/views/index.stub',
        'routes'         => '/stubs/admin/routes/index.stub',
        'config'         => '/stubs/server/config.stub',
        'controller'     => '/stubs/server/controller.stub',
        'install'        => '/stubs/server/install.stub',
        'config_info'    => '/stubs/server/config-info.stub',
        'route'          => '/stubs/server/route.stub',
        'menu_admin'     => '/stubs/server/menu-admin.stub',
        'menu_frontend'  => '/stubs/server/menu-frontend.stub',
        'menu_web'       => '/stubs/server/menu-web.stub',
    ];

    /**
     * 生成插件模板
     *
     * @param string $pluginName        插件名称
     * @param string $pluginTitle       插件标题
     * @param string $pluginDescription 插件描述
     * @param string $frontendType      前端类型，默认为 admin
     *
     * @return array
     */
    public function generatePluginTemplate(string $pluginName, string $pluginTitle, string $pluginDescription = '', string $frontendType = 'admin'): array
    {
        try {
            // 生成前端目录结构
            $this->generateFrontendStructure($pluginName, $frontendType);

            // 生成后端目录结构
            $this->generateBackendStructure($pluginName);

            // 生成前端文件
            $this->generateFrontendFiles($pluginName, $pluginTitle, $pluginDescription, $frontendType);

            // 生成后端文件
            $this->generateBackendFiles($pluginName, $pluginTitle, $pluginDescription);

            return [
                'code'    => 200,
                'message' => '插件模板生成成功',
                'data'    => [
                    'frontend_path' => $this->getPluginFrontendPath($pluginName, $frontendType),
                    'backend_path'  => $this->getBackendPath($pluginName),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => '插件模板生成失败: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * 生成前端目录结构
     *
     * @param string $pluginName   插件名称
     * @param string $frontendType 前端类型
     */
    private function generateFrontendStructure(string $pluginName, string $frontendType): void
    {
        $frontendPath = $this->getPluginFrontendPath($pluginName, $frontendType);

        // 创建前端目录结构（参考 official 模块）
        $directories = [
            $frontendPath . '/api',
            $frontendPath . '/lang/zh-cn',
            $frontendPath . '/lang/en',
            $frontendPath . '/views/index',
            $frontendPath . '/components',
            $frontendPath . '/routes',
        ];

        $this->createDirectories($directories);
    }

    /**
     * 生成后端目录结构
     *
     * @param string $pluginName 插件名称
     */
    private function generateBackendStructure(string $pluginName): void
    {
        $backendPath = $this->getBackendPath($pluginName);

        // 创建后端目录结构（参考 official 模块）
        $directories = [
            $backendPath . '/config',
            $backendPath . '/app/adminapi/controller',
            $backendPath . '/app/api/controller',
            $backendPath . '/app/service/admin',
            $backendPath . '/app/service/api',
            $backendPath . '/app/validate',
            $backendPath . '/app/schema/request',
            $backendPath . '/app/schema/response',
            $backendPath . '/app/dao',
            $backendPath . '/app/model',
            $backendPath . '/public',
            // Resource 目录
            $backendPath . '/resource/database/migrations',
            $backendPath . '/resource/database/seeds',
            $backendPath . '/resource/menu',
            $backendPath . '/resource/template/admin',
            $backendPath . '/resource/template/web',
        ];

        $this->createDirectories($directories);
    }

    /**
     * 批量创建目录
     *
     * @param array $directories 目录路径数组
     */
    private function createDirectories(array $directories): void
    {
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }

    /**
     * 生成前端文件
     *
     * @param string $pluginName        插件名称
     * @param string $pluginTitle       插件标题
     * @param string $pluginDescription 插件描述
     * @param string $frontendType      前端类型
     */
    private function generateFrontendFiles(string $pluginName, string $pluginTitle, string $pluginDescription, string $frontendType): void
    {
        $frontendPath    = $this->getPluginFrontendPath($pluginName, $frontendType);
        $camelPluginName = $this->toCamelCase($pluginName);
        $pluginKey       = $this->toKebabCase($pluginName);

        // 生成 API 文件
        $this->generateApiFile($frontendPath, $pluginName, $pluginKey);

        // 生成语言文件（中英文）
        $this->generateLangFiles($frontendPath, $pluginName, $pluginTitle, $pluginDescription);

        // 生成页面文件（index.vue）
        $this->generatePageFile($frontendPath, $pluginName, $camelPluginName, $pluginKey);

        // 生成 routes/index.ts 文件
        $this->generateRoutesIndexFile($frontendPath, $pluginName, $camelPluginName, $pluginTitle);
    }

    /**
     * 生成后端文件
     *
     * @param string $pluginName        插件名称
     * @param string $pluginTitle       插件标题
     * @param string $pluginDescription 插件描述
     */
    private function generateBackendFiles(string $pluginName, string $pluginTitle, string $pluginDescription): void
    {
        $backendPath     = $this->getBackendPath($pluginName);
        $camelPluginName = $this->toCamelCase($pluginName);
        $pluginKey       = $this->toKebabCase($pluginName);

        // 生成配置文件
        $this->generateConfigFiles($backendPath, $pluginName, $pluginTitle, $pluginDescription);

        // 生成 Install.php 文件
        $this->generateInstallFile($backendPath, $pluginName, $camelPluginName);

        // 生成路由文件（Swagger 注解方式）- 在配置文件生成之后
        $this->generateRouteFile($backendPath, $pluginName, $camelPluginName, $pluginTitle);

        // 生成控制器文件
        $this->generateControllerFile($backendPath, $pluginName, $camelPluginName, $pluginKey);

        // 生成菜单配置文件
        $this->generateMenuFiles($backendPath, $pluginName, $pluginTitle);

        // 生成前端模板到 resource/template/admin 目录
        $this->generateFrontendTemplates($backendPath, $pluginName, $camelPluginName, $pluginKey, $pluginTitle);

        // 初始化公共资源文件
        $this->initializePublicAssets($backendPath);

        // 为其他目录创建 .gitkeep 文件
        $this->createGitKeepFiles($backendPath);
    }

    /**
     * 初始化公共资源文件
     *
     * @param string $backendPath 后端路径
     */
    private function initializePublicAssets(string $backendPath): void
    {
        $publicPath = $backendPath . '/public';

        // 创建 public 目录（如果不存在）
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        // 创建默认的 icon.png 文件（简单透明PNG）
        $iconPath = $publicPath . '/icon.png';
        if (!file_exists($iconPath)) {
            $transparentPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5AgHFA0yMklDQgAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAvElEQVRYw+2W0QqDMBBEbwQv/r/vW7HgVSh4EcSi3mw2SQm0ONjZzQfOwsLMZDLZZjAYuNfr1f6ZJCImIpqmeX+1WnUDuL8AACYFQRCgqqo+AFRV1d+dw+FoAQAiQlEUPQB4PB5YLpcAgMViAcMwIIoiHo/HXwGA4jgcDiiKYgiAuq6RZRmyLOsBoGkaEZHf9/0AAJqmQRRFaNsWvu/Dtm0wxkBKS0sp6bquCyJaEkXRLwCqqkIURei6DkVRQCl1CYBhGFBVFYqiAKX0JgBSSnRdh6IoEEURGGPKA4BlWZBSou/7SwCklEiSBEmSgDHmNgBSSnRdh6IoYIzpAWCMQRQJaa27AUAIIYQQQgghhBBCCCHf8wL0taA/yDgfmQAAAABJRU5ErkJggg==');
            file_put_contents($iconPath, $transparentPng);
        }

        // 创建默认的 cover.png 文件（简单蓝色PNG）
        $coverPath = $publicPath . '/cover.png';
        if (!file_exists($coverPath)) {
            $bluePng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5AgHFA4ZEwgJOwAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAyUlEQVRYw+2WsQrCQBCGP1+hFhYWaS0sLCz8gIWFhYWFrQVBsLQQFEQLCwuxsBAEwUISBEELC0EQBMHvJGcnF8gFNzezm82+ZTYzM8nswvx+xJjFGDPHcYwxxlLK+LZKKSGEGMcx5pzjnBs3ACmlEEKMc05rbTcAUsrY7/cwxmCMQWttFwCtNVhr0VrDGNMNgNYarTWMMTDGYIyxCwBrLUKIUErBGINSCq01WmtQSsE5hzEGay2cc2OMMcYY/xZAKQXn3BiGAADGGDjnMMaglILWGkopOOcwxsAYgzEGYwycc3DOjf8CQAhhrLVwzuGcQ2uN1hqcc3DOYYxBKYXWWjjn4JyDc278VgDGGLTWcM6NzjmstXDODc45OOcwxiCEgNYarTU45+Ccg3Nu/BYAay2cc3DOIYSAtRbOOYwxCCGgtYYxBmMMnHMwxmCthXMOzjk45+Ccg/M/AKy1cM7BOYcQAloLnHNwzsE5ByEEtNZgjMFaC2MMnHMwxmCtgXMOzjk45+Ccg/M/AKy1cM7BOYcQAloLnHNwzsE5ByEEtNZgjMFaC+ccnHNwzsE5Nzjn4JyDcw7OuTHG+DMAAP//AwDclW/p/qcTdAAAAABJRU5ErkJggg==');
            file_put_contents($coverPath, $bluePng);
        }
    }

    /**
     * 生成 API 文件
     *
     * @param string $frontendPath 前端路径
     * @param string $pluginName   插件名称
     * @param string $pluginKey    插件键名
     */
    private function generateApiFile(string $frontendPath, string $pluginName, string $pluginKey): void
    {
        $stubPath = __DIR__ . self::STUB_PATHS['api'];
        $content  = $this->renderStub($stubPath, [
            '{{pluginName}}' => $pluginName,
            '{{pluginKey}}'  => $pluginKey,
        ]);
        file_put_contents($frontendPath . '/api/index.ts', $content);
    }

    /**
     * 生成语言文件（中英文）
     *
     * @param string $frontendPath      前端路径
     * @param string $pluginName        插件名称
     * @param string $pluginTitle       插件标题
     * @param string $pluginDescription 插件描述
     */
    private function generateLangFiles(string $frontendPath, string $pluginName, string $pluginTitle, string $pluginDescription): void
    {
        // 中文语言包
        $zhContent = "{\n  \"{$pluginName}\": {\n    \"title\": \"{$pluginTitle}\",\n    \"description\": \"{$pluginDescription}\"\n  }\n}";
        file_put_contents($frontendPath . '/lang/zh-cn/index.json', $zhContent);

        // 英文语言包
        $enContent = "{\n  \"{$pluginName}\": {\n    \"title\": \"{$pluginTitle}\",\n    \"description\": \"{$pluginDescription}\"\n  }\n}";
        file_put_contents($frontendPath . '/lang/en/index.json', $enContent);
    }

    /**
     * 生成页面文件
     *
     * @param string $frontendPath    前端路径
     * @param string $pluginName      插件名称
     * @param string $camelPluginName 驼峰命名的插件名称
     * @param string $pluginKey       插件键名
     */
    private function generatePageFile(string $frontendPath, string $pluginName, string $camelPluginName, string $pluginKey): void
    {
        $stubPath = __DIR__ . self::STUB_PATHS['view'];
        $content  = $this->renderStub($stubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
            '{{pluginKey}}'       => $pluginKey,
        ]);
        file_put_contents($frontendPath . '/views/index/index.vue', $content);
    }

    /**
     * 生成路由文件
     *
     * @param string $frontendPath     前端路径
     * @param string $pluginName       插件名称
     * @param string $camelPluginName 驼峰命名的插件名称
     * @param string $pluginTitle      插件标题
     */
    private function generateRoutesIndexFile(string $frontendPath, string $pluginName, string $camelPluginName, string $pluginTitle): void
    {
        $stubPath = __DIR__ . self::STUB_PATHS['routes'];
        $content = $this->renderStub($stubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
            '{{pluginTitle}}'     => $pluginTitle,
        ]);
        file_put_contents($frontendPath . '/routes/index.ts', $content);
    }

    /**
     * 生成配置文件
     *
     * @param string $backendPath       后端路径
     * @param string $pluginName        插件名称
     * @param string $pluginTitle       插件标题
     * @param string $pluginDescription 插件描述
     */
    private function generateConfigFiles(string $backendPath, string $pluginName, string $pluginTitle, string $pluginDescription): void
    {
        // app.php 配置
        $appStubPath = __DIR__ . self::STUB_PATHS['config'];
        $appContent  = $this->renderStub($appStubPath, [
            '{{pluginName}}'        => $pluginName,
            '{{pluginTitle}}'       => $pluginTitle,
            '{{pluginDescription}}' => $pluginDescription,
        ]);
        file_put_contents($backendPath . '/config/app.php', $appContent);

        // info.php 配置
        $infoStubPath = __DIR__ . self::STUB_PATHS['config_info'];
        $infoContent  = $this->renderStub($infoStubPath, [
            '{{pluginName}}'        => $pluginName,
            '{{pluginTitle}}'       => $pluginTitle,
            '{{pluginDescription}}' => $pluginDescription,
        ]);
        file_put_contents($backendPath . '/config/info.php', $infoContent);
    }

    /**
     * 生成 Install.php 文件
     *
     * @param string $backendPath     后端路径
     * @param string $pluginName      插件名称
     * @param string $camelPluginName 驼峰命名的插件名称
     */
    private function generateInstallFile(string $backendPath, string $pluginName, string $camelPluginName): void
    {
        $stubPath = __DIR__ . self::STUB_PATHS['install'];
        $content  = $this->renderStub($stubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
        ]);
        file_put_contents($backendPath . '/Install.php', $content);
    }

    /**
     * 生成路由文件（Swagger 注解方式）
     *
     * @param string $backendPath     后端路径
     * @param string $pluginName      插件名称
     * @param string $camelPluginName 驼峰命名的插件名称
     * @param string $pluginTitle    插件标题
     */
    private function generateRouteFile(string $backendPath, string $pluginName, string $camelPluginName, string $pluginTitle): void
    {
        $stubPath = __DIR__ . self::STUB_PATHS['route'];
        $pluginKey = $this->toKebabCase($pluginName);
        $content  = $this->renderStub($stubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
            '{{pluginKey}}'       => $pluginKey,
            '{{pluginTitle}}'     => $pluginTitle,
        ]);
        file_put_contents($backendPath . '/config/route.php', $content);
    }

    /**
     * 生成控制器文件
     *
     * @param string $backendPath     后端路径
     * @param string $pluginName      插件名称
     * @param string $camelPluginName 驼峰命名的插件名称
     * @param string $pluginKey       插件键名
     */
    private function generateControllerFile(string $backendPath, string $pluginName, string $camelPluginName, string $pluginKey): void
    {
        $stubPath = __DIR__ . self::STUB_PATHS['controller'];
        $content  = $this->renderStub($stubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
            '{{pluginKey}}'       => $pluginKey,
        ]);
        file_put_contents($backendPath . '/app/adminapi/controller/' . $camelPluginName . 'Controller.php', $content);
    }

    /**
     * 为目录创建 .gitkeep 文件
     *
     * @param string $backendPath 后端路径
     */
    private function createGitKeepFiles(string $backendPath): void
    {
        $directories = [
            '/app/schema/request',
            '/app/schema/response',
            '/app/dao',
            '/app/model',
            '/app/service/admin',
            '/app/service/api',
            '/app/validate',
            '/app/api/controller',
            '/resource/database/migrations',
            '/resource/database/seeds',
            '/resource/template/admin',
            '/resource/template/web',
        ];

        foreach ($directories as $path) {
            $directory = $backendPath . $path;
            if (is_dir($directory)) {
                file_put_contents($directory . '/.gitkeep', '');
            }
        }
    }

    /**
     * 生成菜单配置文件
     *
     * @param string $backendPath 后端路径
     * @param string $pluginName  插件名称
     * @param string $pluginTitle 插件标题
     */
    private function generateMenuFiles(string $backendPath, string $pluginName, string $pluginTitle): void
    {
        $pluginKey = $this->toKebabCase($pluginName);

        // 生成 admin 菜单
        $adminStubPath = __DIR__ . self::STUB_PATHS['menu_admin'];
        $adminContent  = $this->renderStub($adminStubPath, [
            '{{pluginName}}'  => $pluginName,
            '{{pluginKey}}'   => $pluginKey,
            '{{pluginTitle}}' => $pluginTitle,
        ]);
        file_put_contents($backendPath . '/resource/menu/admin.php', $adminContent);

        // 生成 frontend 菜单
        $frontendStubPath = __DIR__ . self::STUB_PATHS['menu_frontend'];
        $frontendContent  = $this->renderStub($frontendStubPath, [
            '{{pluginName}}'  => $pluginName,
            '{{pluginKey}}'   => $pluginKey,
            '{{pluginTitle}}' => $pluginTitle,
        ]);
        file_put_contents($backendPath . '/resource/menu/frontend.php', $frontendContent);

        // 生成 web 菜单
        $webStubPath = __DIR__ . self::STUB_PATHS['menu_web'];
        $webContent  = $this->renderStub($webStubPath, [
            '{{pluginName}}'  => $pluginName,
            '{{pluginKey}}'   => $pluginKey,
            '{{pluginTitle}}' => $pluginTitle,
        ]);
        file_put_contents($backendPath . '/resource/menu/web.php', $webContent);
    }

    /**
     * 生成前端模板到 resource/template/admin 目录
     *
     * @param string $backendPath     后端路径
     * @param string $pluginName       插件名称
     * @param string $camelPluginName 驼峰命名的插件名称
     * @param string $pluginKey       插件键名
     * @param string $pluginTitle      插件标题
     */
    private function generateFrontendTemplates(string $backendPath, string $pluginName, string $camelPluginName, string $pluginKey, string $pluginTitle): void
    {
        $templatePath = $backendPath . '/resource/template/admin';

        // 创建模板目录结构
        $templateDirectories = [
            $templatePath . '/api',
            $templatePath . '/lang/zh-cn',
            $templatePath . '/lang/en',
            $templatePath . '/views/index',
            $templatePath . '/components',
            $templatePath . '/routes',
        ];

        foreach ($templateDirectories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        // 生成 API 文件
        $apiStubPath = __DIR__ . self::STUB_PATHS['api'];
        $apiContent = $this->renderStub($apiStubPath, [
            '{{pluginName}}' => $pluginName,
            '{{pluginKey}}'  => $pluginKey,
        ]);
        file_put_contents($templatePath . '/api/index.ts', $apiContent);

        // 生成语言文件
        $zhContent = "{\n  \"{$pluginName}\": {\n    \"title\": \"{$pluginTitle}\",\n    \"description\": \"Plugin description\"\n  }\n}";
        file_put_contents($templatePath . '/lang/zh-cn/index.json', $zhContent);

        $enContent = "{\n  \"{$pluginName}\": {\n    \"title\": \"{$pluginTitle}\",\n    \"description\": \"Plugin description\"\n  }\n}";
        file_put_contents($templatePath . '/lang/en/index.json', $enContent);

        // 生成页面文件
        $viewStubPath = __DIR__ . self::STUB_PATHS['view'];
        $viewContent = $this->renderStub($viewStubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
            '{{pluginKey}}'       => $pluginKey,
        ]);
        file_put_contents($templatePath . '/views/index/index.vue', $viewContent);

        // 生成路由文件
        $routesStubPath = __DIR__ . self::STUB_PATHS['routes'];
        $routesContent = $this->renderStub($routesStubPath, [
            '{{pluginName}}'      => $pluginName,
            '{{camelPluginName}}' => $camelPluginName,
            '{{pluginTitle}}'     => $pluginTitle,
        ]);
        file_put_contents($templatePath . '/routes/index.ts', $routesContent);
    }

    /**
     * 渲染模板文件
     *
     * @param string $stubPath     模板文件路径
     * @param array  $replacements 替换内容数组
     *
     * @return string 渲染后的内容
     */
    private function renderStub(string $stubPath, array $replacements): string
    {
        if (!file_exists($stubPath)) {
            throw new \RuntimeException("模板文件不存在: {$stubPath}");
        }

        $content = file_get_contents($stubPath);
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    /**
     * 转换为驼峰命名
     *
     * @param string $string 原始字符串
     *
     * @return string 驼峰命名的字符串
     */
    private function toCamelCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * 转换为短横线命名
     *
     * @param string $string 原始字符串
     *
     * @return string 短横线命名的字符串
     */
    private function toKebabCase(string $string): string
    {
        return strtolower(str_replace('_', '-', $string));
    }

    /**
     * 获取插件前端路径
     *
     * @param string $pluginName   插件名称
     * @param string $frontendType 前端类型，默认为 admin
     *
     * @return string
     */
    private function getPluginFrontendPath(string $pluginName, string $frontendType = 'admin'): string
    {
        // 使用 base_path() 获取项目根目录
        $basePath = base_path();

        // 处理已知类型
        if (FrontendType::isKnownType($frontendType)) {
            $typeEnum = FrontendType::from($frontendType);
            return dirname($basePath) . '/' . sprintf($typeEnum->pathTemplate(), $pluginName);
        }
        // 处理未知类型（使用默认格式）
        return dirname($basePath) . "/{$frontendType}/src/apps/{$pluginName}";
    }

    /**
     * 构建插件
     *
     * @param string      $pluginKey       插件键名（kebab-case 格式，如 test-demo）
     * @param string|null $targetDirectory 目标目录路径（可选，默认为 runtime/plugins）
     *
     * @return array
     */
    public function build(string $pluginKey, ?string $targetDirectory = null): array
    {
        try {
            // 将 kebab-case 转换为 snake_case 作为插件名称
            $pluginName = str_replace('-', '_', $pluginKey);

            // 定义路径
            $sourceBackendPath  = $this->getBackendPath($pluginName);
            // 尝试不同的前端类型路径
            $frontendTypes = ['admin', 'web'];
            $foundFrontendPath = null;
            $foundFrontendType = null;

            foreach ($frontendTypes as $type) {
                $path = $this->getPluginFrontendPath($pluginName, $type);
                if (is_dir($path)) {
                    $foundFrontendPath = $path;
                    $foundFrontendType = $type;
                    break;
                }
            }

            // 使用临时构建目录,不删除插件本身
            $buildPath = runtime_path(self::getPluginBuildPath() . '/' . $pluginKey);

            // 确定目标目录
            if ($targetDirectory === null) {
                $targetDirectory = runtime_path(self::getPluginPackagesPath());
            }

            // 确保目标目录存在
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            $zipFilePath = $targetDirectory . '/' . $pluginKey . '.zip';

            // 验证源目录是否存在
            if (!is_dir($sourceBackendPath)) {
                throw new \RuntimeException("插件源目录不存在: {$sourceBackendPath}");
            }

            // 创建构建目录结构
            $this->createBuildDirectories($buildPath);

            // 复制后端资源
            $this->copyDirectory($sourceBackendPath . '/app', $buildPath . '/app');
            $this->copyDirectory($sourceBackendPath . '/config', $buildPath . '/config');
            // 复制其他必要文件(如 Install.php, public, resource 等)
            $this->copyFileIfExists($sourceBackendPath . '/Install.php', $buildPath . '/Install.php');
            if (is_dir($sourceBackendPath . '/public')) {
                $this->copyDirectory($sourceBackendPath . '/public', $buildPath . '/public');
            }
            if (is_dir($sourceBackendPath . '/resource')) {
                $this->copyDirectory($sourceBackendPath . '/resource', $buildPath . '/resource');
            }

            // 复制前端资源到 resource/template 目录
            if ($foundFrontendPath && $foundFrontendType) {
                $templateTargetDir = $buildPath . '/resource/template/' . $foundFrontendType;
                if (is_dir($templateTargetDir)) {
                    $this->copyDirectory($foundFrontendPath, $templateTargetDir);
                }
            }

            // 压缩构建结果为 zip 文件
            $this->createZipArchive($buildPath, $zipFilePath);

            // 清理构建目录
            $this->deleteDirectory($buildPath);

            return [
                'code'    => 200,
                'message' => '插件构建成功',
                'data'    => [
                    'build_path'       => $buildPath,
                    'target_directory' => $targetDirectory,
                    'zip_file_path'    => $zipFilePath,
                    'plugin_key'       => $pluginKey,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => '插件构建失败: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * 复制目录
     *
     * @param string $source 源目录
     * @param string $target 目标目录
     */
    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourceFile = $source . '/' . $file;
            $targetFile = $target . '/' . $file;

            if (is_dir($sourceFile)) {
                $this->copyDirectory($sourceFile, $targetFile);
            } else {
                copy($sourceFile, $targetFile);
            }
        }
    }

    /**
     * 复制文件(如果存在)
     *
     * @param string $sourceFile 源文件路径
     * @param string $targetFile 目标文件路径
     */
    private function copyFileIfExists(string $sourceFile, string $targetFile): void
    {
        if (file_exists($sourceFile)) {
            // 确保目标目录存在
            $targetDir = dirname($targetFile);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            copy($sourceFile, $targetFile);
        }
    }

    /**
     * 删除目录
     *
     * @param string $directory 目录路径
     */
    private function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $directory . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }

    /**
     * 创建 ZIP 压缩文件
     *
     * @param string $sourcePath  源目录路径
     * @param string $zipFilePath ZIP 文件路径
     */
    private function createZipArchive(string $sourcePath, string $zipFilePath): void
    {
        // 如果 ZIP 文件已存在，先删除
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        // 创建 ZIP 归档对象
        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("无法创建 ZIP 文件: {$zipFilePath}");
        }

        // 获取源目录的绝对路径
        $sourcePath = realpath($sourcePath);
        if ($sourcePath === false) {
            throw new \RuntimeException("无法获取源目录的绝对路径: {$sourcePath}");
        }

        // 遍历源目录并添加文件到 ZIP
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            // 跳过目录
            if (!$file->isFile()) {
                continue;
            }

            // 获取文件的绝对路径
            $filePath = $file->getRealPath();
            if ($filePath === false) {
                continue;
            }

            // 计算 ZIP 中的相对路径
            $relativePath = substr($filePath, strlen($sourcePath) + 1);

            // 添加文件到 ZIP
            if (!$zip->addFile($filePath, $relativePath)) {
                throw new \RuntimeException("无法添加文件到 ZIP: {$filePath}");
            }
        }

        // 关闭 ZIP 归档
        if (!$zip->close()) {
            throw new \RuntimeException("无法关闭 ZIP 文件: {$zipFilePath}");
        }
    }

    /**
     * 获取后端路径
     *
     * @param string $pluginName 插件名称
     *
     * @return string
     */
    private function getBackendPath(string $pluginName): string
    {
        return base_path() . '/plugin/' . $pluginName;
    }

    /**
     * 创建构建目录结构
     *
     * @param string $buildPath 构建路径
     */
    private function createBuildDirectories(string $buildPath): void
    {
        $directories = [
            $buildPath . '/app/controller',
            $buildPath . '/app/service/admin',
            $buildPath . '/app/service/api',
            $buildPath . '/app/validate',
            $buildPath . '/app/dao',
            $buildPath . '/app/model',
            $buildPath . '/app/schema/request',
            $buildPath . '/app/schema/response',
            $buildPath . '/config',
            $buildPath . '/resource/template/admin',
            $buildPath . '/resource/template/web',
        ];

        $this->createDirectories($directories);
    }
}