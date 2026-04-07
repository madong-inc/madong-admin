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

use app\dao\plugin\PluginDao;
use support\Container;

/**
 * 插件服务类
 *
 * @package app\service\core\plugin
 */
final class PluginService extends PluginBaseService
{

    /**
     * 插件根目录
     */
    protected string $plugin_path;

    /**
     * 项目根目录（前端和后端的父级目录）
     */
    protected string $project_path;

    /**
     * 后端根目录
     */
    protected string $server_path;

    public function __construct(PluginDao $dao)
    {
        // 初始化基础路径
        $this->plugin_path = base_path('plugin');
        $this->project_path = dirname(base_path()); // 项目根目录（前端和后端的父级）
        $this->server_path = base_path(); // 后端根目录
        $this->dao = $dao;
    }

    /**
     * 获取插件列表
     *
     * @param string|null $category 插件分类（all/installed/un_installed/purchased/updatable）
     * @param string|null $type     插件类型
     * @param string|null $keyword  搜索关键词
     * @param int         $page     页码
     * @param int         $limit    每页数量
     *
     * @return array 插件列表
     */
    public function getList(string|null $category = 'all', string|null $type = null, string|null $keyword = null, int $page = 1, int $limit = 9999): array
    {
        // 获取授权配置
        $config = [
            'auth_code'   => config('madong.auth_code', ''),
            'auth_secret' => config('madong.auth_secret', ''),
            'page'        => $page,
            'limit'       => $limit,
            'market_host' => config('madong.market_host', 'https://madong.tech'),
            'name'        => $keyword,
        ];
        // 获取本地插件列表（原始数据）
        $localModules = $this->getLocalModules($config['auth_code']);
        /** @var PluginRemoteService $pluginRemoteService */
        // 获取已购买的插件列表（原始数据）
        $pluginRemoteService = Container::make(PluginRemoteService::class);
        $purchasedModules    = $pluginRemoteService->getPurchasedModules($config);
        // 从本地插件中派生出已安装的模块
        $installedModules = array_filter($localModules, function ($module) {
            return $module['is_installed'] === true;
        });
        // 构建已安装插件的映射
        $installedMap = array_column($installedModules, null, 'name');

        // 合并本地和远程市场的插件
        $allModules = $this->mergeModules($localModules, $purchasedModules);

        // 根据不同类型处理数据
        $processedItems = [];
        switch ($category) {
            case 'installed':
                // 已安装（本地已安装状态）
                $processedItems = $this->getInstalledModulesData($allModules, $installedMap, $installedModules);
                break;
            case 'un_installed':
                // 未安装（本地未安装状态）
                $processedItems = $this->getUninstalledModulesData($localModules, $installedMap);
                break;
            case 'purchased':
                // 已购买（远程市场返回的所有）
                $processedItems = $this->getPurchasedModulesData($purchasedModules, $installedMap);
                break;
            case 'updatable':
                // 可更新（远程市场跟本地安装对比 市场版本大于安装版本的状态）
                $processedItems = $this->getUpdatableModulesData($allModules, $installedMap, $installedModules);
                break;
            case 'all':
            default:
                // 全部（本地+市场）
                $processedItems = $this->getAllModulesData($allModules, $installedMap);
                break;
        }

        // 应用通用过滤
        $filteredItems = [];
        foreach ($processedItems as $module) {
            if ($type !== null && (string)$module['status'] !== (string)$type) {
                continue;
            }
            if ($keyword && !$this->matchKeyword($module, $keyword)) {
                continue;
            }
            $filteredItems[] = $module;
        }

        // 分页处理
        $total = count($filteredItems);
        $items = array_slice($filteredItems, ($page - 1) * $limit, $limit);
        return compact('page', 'limit', 'total', 'items');
    }

    /**
     * 合并本地和远程市场的插件
     *
     * @param array $localModules     本地插件列表
     * @param array $purchasedModules 已购买的插件列表
     *
     * @return array 合并后的插件列表
     */
    private function mergeModules(array $localModules, array $purchasedModules): array
    {
        $merged    = [];
        $moduleMap = [];

        // 先添加本地插件
        foreach ($localModules as $module) {
            $merged[]                   = $module;
            $moduleMap[$module['name']] = count($merged) - 1;
        }

        // 再添加远程市场的插件（如果本地没有）
        foreach ($purchasedModules as $module) {
            if (!isset($moduleMap[$module['name']])) {
                $merged[] = $module;
            }
        }

        return $merged;
    }

    /**
     * 获取本地插件列表（从 plugin 目录扫描）
     *
     * @param string $authCode 授权码
     *
     * @return array
     */
    public function getLocalModules(string $authCode = ''): array
    {
        $modules    = [];
        $pluginPath = $this->plugin_path;

        if (!is_dir($pluginPath)) {
            return $modules;
        }

        $dirItems = scandir($pluginPath);
        foreach ($dirItems as $key => $item) {
            if ($item === '.' || $item === '..' || !is_dir($pluginPath . DIRECTORY_SEPARATOR . $item)) {
                continue;
            }

            $itemPath      = $pluginPath . DIRECTORY_SEPARATOR . $item;
            $infoPath      = $itemPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'info.php';
            $installedPath = $itemPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'installed.php';
            $publicPath    = $itemPath . DIRECTORY_SEPARATOR . 'public';

            // 只读取 info.php
            if (!file_exists($infoPath)) {
                continue;
            }

            $config = include $infoPath;
            if (!is_array($config)) {
                continue;
            }

            // 检查插件归属类型
            $pluginType = $config['type'] ?? 'madong';
            if (!str_starts_with($pluginType, 'madong:') && $pluginType !== 'madong') {
                continue;
            }

            // 检查安装状态 - 通过 installed.php 判断
            $isInstalled = file_exists($installedPath);
            $installedAt = 0;
            if ($isInstalled) {
                $installedConfig = include $installedPath;
                if (is_array($installedConfig) && !empty($installedConfig['installed_at'])) {
                    $installedAt = strtotime($installedConfig['installed_at']);
                } else {
                    $installedAt = time();
                }
            }

            // 获取 icon 和 cover
            $icon  = '';
            $cover = '';
            if (is_dir($publicPath)) {
                if (file_exists($publicPath . '/icon.png')) {
                    $icon = base64_encode(file_get_contents($publicPath . '/icon.png'));
                }
                if (file_exists($publicPath . '/cover.png')) {
                    $cover = base64_encode(file_get_contents($publicPath . '/cover.png'));
                }
            }

            $modules[] = [
                'id'           => $key,
                'code'         => $config['name'] ?? $item,
                'name'         => $config['name'] ?? $item,
                'type'         => $config['type'] ?? 'madong',
                'version'      => $config['version'] ?? '1.0.0',
                'status'       => $isInstalled ? 1 : 0,
                'description'  => $config['description'] ?? '',
                'author'       => $config['author'] ?? '',
                'price'        => 0,
                'cover'        => $cover,
                'icon'         => $icon,
                'downloads'    => 0,
                'rating'       => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
                'auth_code'    => $authCode,
                'is_installed' => $isInstalled,
                'installed_at' => $installedAt,
                'is_local'     => 1,
                'undeletable'  => isset($config['uninstall']['undeletable']) && $config['uninstall']['undeletable'] === true ? 1 : 0,
            ];
        }

        return $modules;
    }

    /**
     * 获取已安装模块数据
     */
    private function getInstalledModulesData(array $allModules, array $installedMap, array $installedModules): array
    {
        $result = [];
        foreach ($installedModules as $localModule) {
            // 从localModules派生的installedModules可能没有enable字段，默认为true
            $enable = $localModule['enable'] ?? true;
            if (!$enable) continue;

            $name         = $localModule['name'];
            $remoteModule = $this->findModuleByName($allModules, $name);

            $result[] = $this->buildModuleItem(
                $remoteModule ?: $this->createFallbackModule($localModule),
                $localModule,
                $installedMap
            );
        }
        return $result;
    }

    /**
     * 获取未安装模块数据
     */
    private function getUninstalledModulesData(array $localModules, array $installedMap): array
    {
        $result = [];
        foreach ($localModules as $module) {
            if (isset($installedMap[$module['name']])) continue;

            $result[] = $this->buildModuleItem($module, null, $installedMap);
        }
        return $result;
    }

    /**
     * 获取已购买模块数据
     */
    private function getPurchasedModulesData(array $purchasedModules, array $installedMap): array
    {
        $result = [];
        foreach ($purchasedModules as $module) {
            $localModule = $installedMap[$module['name']] ?? null;
            $result[]    = $this->buildModuleItem($module, $localModule, $installedMap);
        }
        return $result;
    }

    /**
     * 获取可更新模块数据
     */
    private function getUpdatableModulesData(array $allModules, array $installedMap, array $installedModules): array
    {
        $result = [];
        foreach ($installedModules as $localModule) {
            // 从localModules派生的installedModules可能没有enable字段，默认为true
            $enable = $localModule['enable'] ?? true;
            if (!$enable) continue;

            $name         = $localModule['name'];
            $remoteModule = $this->findModuleByName($allModules, $name);

            if (!$remoteModule) continue;

            if (version_compare($remoteModule['version'], $localModule['version'], '>')) {
                $result[] = $this->buildModuleItem($remoteModule, $localModule, $installedMap);
            }
        }
        return $result;
    }

    /**
     * 获取所有模块数据
     */
    private function getAllModulesData(array $allModules, array $installedMap): array
    {
        $result = [];
        foreach ($allModules as $module) {
            $localModule = $installedMap[$module['name']] ?? null;
            $result[]    = $this->buildModuleItem($module, $localModule, $installedMap);
        }
        return $result;
    }

    /**
     * 构建模块数据项（包含安装状态、版本差异等）
     */
    private function buildModuleItem(?array $remoteModule, ?array $localModule, array $installedMap): array
    {
        $name          = $remoteModule['name'] ?? $localModule['name'] ?? '';
        $isInstalled   = $localModule !== null;
        $localVersion  = $isInstalled ? $localModule['version'] : null;
        $remoteVersion = $remoteModule['version'] ?? null;

        // 获取本地插件标志
        $isLocal = ($localModule && isset($localModule['is_local'])) ? $localModule['is_local'] : 0;

        // 获取不可删除标志（优先从 localModule 获取）
        $undeletable = 0;
        if ($localModule && isset($localModule['undeletable'])) {
            $undeletable = $localModule['undeletable'];
        }

        // 计算可更新状态
        $hasUpdate         = false;
        $versionComparison = null;
        if ($isInstalled && $remoteVersion) {
            $hasUpdate         = version_compare($remoteVersion, $localVersion, '>');
            $versionComparison = $hasUpdate ? "{$remoteVersion} > {$localVersion}" : null;
        }

        // 计算购买状态
        $isPurchased = false;
        if ($remoteModule) {
            $isPurchased = $remoteModule['price'] == 0 ||
                in_array($remoteModule['name'], ['sms-verification', 'wechat-integration']);
        }

        // 统一数据结构 - 使用专业命名
        return [
            'id'                    => $remoteModule['id'] ?? $localModule['name'] ?? '',
            'code'                  => $remoteModule['code'] ?? $localModule['name'] ?? '',
            'name'                  => $name,
            'type'                  => $remoteModule['type'] ?? ($localModule['type'] ?? 'module'),
            'version'               => $remoteVersion ?? $localVersion ?? '',
            'status'                => $remoteModule['status'] ?? 1,
            'description'           => $remoteModule['description'] ?? ($localModule['description'] ?? ''),
            'detail_description'    => $remoteModule['detail_description'] ?? '',
            'author'                => $remoteModule['author'] ?? ($localModule['author'] ?? ''),
            'cover'                 => $remoteModule['cover'] ?? '',
            'poster'                => $remoteModule['poster'] ?? '',
            'price'                 => $remoteModule['price'] ?? 0,
            'downloads'             => $remoteModule['downloads'] ?? 0,
            'rating'                => $remoteModule['rating'] ?? 0,
            'created_at'            => $remoteModule['created_at'] ?? date('Y-m-d H:i:s'),
            'updated_at'            => $remoteModule['updated_at'] ?? date('Y-m-d H:i:s'),
            'update_time'           => $remoteModule['update_time'] ?? '',
            'update_logs'           => $remoteModule['update_logs'] ?? [],
            'category'              => $remoteModule['category'] ?? null,
            'category_name'         => $remoteModule['category_name'] ?? '',
            'tags'                  => $remoteModule['tags'] ?? [],
            'is_new'                => (int)($remoteModule['is_new'] ?? 0),
            'is_hot'                => (int)($remoteModule['is_hot'] ?? 0),
            'purchased'             => (int)($remoteModule['purchased'] ?? 0),
            'installed'             => (int)($remoteModule['installed'] ?? 0),
            'manual_uninstall'      => (int)($remoteModule['manual_uninstall'] ?? 0),
            'composer_dependencies' => $remoteModule['composer_dependencies'] ?? [],
            'npm_dependencies'      => $remoteModule['npm_dependencies'] ?? [],

            // 状态标识 - 使用更专业的命名
            'is_installed'          => (int)$isInstalled,
            'installed_version'     => $localVersion,
            'has_update'            => (int)$hasUpdate,
            'is_purchased'          => (int)$isPurchased,
            'is_downloaded'         => (int)($isInstalled || in_array($name, ['data-export'])),
            'can_uninstall'         => (int)($isInstalled && $name !== 'user-management'),
            'can_download'          => (int)$isPurchased,
            'is_local'              => (int)$isLocal,
            'undeletable'           => $undeletable,

            // 专业化的版本信息结构
            'version_info'          => [
                'remote'     => [
                    'version'      => $remoteVersion,
                    'release_date' => $remoteModule['updated_at'] ?? null,
                ],
                'local'      => [
                    'version'      => $localVersion,
                    'install_date' => $localModule['created_at'] ?? null,
                ],
                'comparison' => [
                    'needs_update'       => (int)$hasUpdate,
                    'is_latest'          => (int)($isInstalled && $remoteVersion && version_compare($remoteVersion, $localVersion, '<=')),
                    'version_difference' => $versionComparison,
                ],
            ]
        ];
    }

    /**
     * 通过名称查找模块
     */
    private function findModuleByName(array $modules, string $name): ?array
    {
        foreach ($modules as $module) {
            if ($module['name'] === $name) {
                return $module;
            }
        }
        return null;
    }

    /**
     * 创建回退模块（当远程模块不存在时使用）
     */
    private function createFallbackModule(array $localModule): array
    {
        return [
            'name'        => $localModule['name'],
            'type'        => 'module',
            'version'     => $localModule['version'],
            'status'      => 1,
            'description' => $localModule['description'] ?? '',
            'author'      => $localModule['author'] ?? '',
            'price'       => 0,
            'cover'       => '',
            'downloads'   => 0,
            'rating'      => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
            'undeletable' => $localModule['undeletable'] ?? 0,
        ];
    }

    /**
     * 匹配模块名称、描述和作者是否包含关键词
     */
    private function matchKeyword(array $module, string $keyword): bool
    {
        $lowerKeyword = strtolower($keyword);
        return str_contains(strtolower($module['name']), $lowerKeyword) ||
            str_contains(strtolower($module['description']), $lowerKeyword) ||
            str_contains(strtolower($module['author']), $lowerKeyword);
    }

    /**
     * 获取模块升级日志
     *
     * @param string $moduleName 模块名称
     *
     * @return array 升级日志列表
     */
    public function getUpgradeLogs(string $moduleName): array
    {
        try {
            // 获取授权配置
            $config = [
                'auth_code'   => config('madong.auth_code', ''),
                'auth_secret' => config('madong.auth_secret', ''),
                'market_host' => config('madong.market_host', 'https://madong.tech'),
            ];

            /** @var PluginRemoteService $pluginRemoteService */
            $pluginRemoteService = Container::make(PluginRemoteService::class);
            return $pluginRemoteService->getRemoteUpdateLogs(
                $config['auth_code'],
                $config['auth_secret'],
                $moduleName
            );

        } catch (\Throwable $e) {
            return [];
        }
    }
}