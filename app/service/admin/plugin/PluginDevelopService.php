<?php
declare(strict_types=1);
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\service\admin\plugin;

use app\dao\plugin\PluginDao;
use app\model\plugin\Plugin;
use app\process\Monitor;
use core\base\BaseService;
use core\exception\handler\AdminException;
use core\uuid\Snowflake;
use support\Container;

/**
 * Plugin服务层
 *
 * @author Mr.April
 * @since  1.0
 */
class PluginDevelopService extends BaseService
{
    private string $pluginDir;
    private string $adminDir;
    private string $webDir;

    public function __construct(PluginDao $dao)
    {
        $this->dao       = $dao;
        $basePath      = base_path();
        $projectPath  = dirname($basePath);
        $frontendPath = $projectPath . '/frontend';

        $this->pluginDir = $basePath . '/plugin';
        $this->adminDir  = $frontendPath . '/admin/src/apps';
        $this->webDir    = $frontendPath . '/web/app/apps';
    }  

    /**
     * 获取插件列表（扫描插件目录并同步到数据库）
     *
     * @param array $where 查询条件（标准filters格式）
     * @param int   $page  页码
     * @param int   $limit 每页数量
     *
     * @return array
     * @throws \Exception
     */
    public function getList(array $where = [], int $page = 1, int $limit = 15): array
    {
        // 先扫描插件目录，将新插件同步到数据库
        $this->syncPlugins();
        return $this->dao->getList($where, $page, $limit);
    }

    /**
     * 扫描插件目录并同步到数据库
     *
     * @return void
     * @throws \Exception
     */
    private function syncPlugins(): void
    {
        // 获取插件根目录下所有子目录
        if (!is_dir($this->pluginDir)) {
            return;
        }

        $dirs               = scandir($this->pluginDir);
        $pluginsToInsert    = [];
        $existingPluginKeys = [];

        foreach ($dirs as $dir) {
            // 跳过.和..目录
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            // 优先读取 info.php 获取插件基本信息
            $infoPath = $this->pluginDir . '/' . $dir . '/config/info.php';
            if (!file_exists($infoPath)) {
                continue;
            }

            // 加载插件 info 配置
            $infoConfig = include $infoPath;
            if (!is_array($infoConfig)) {
                continue;
            }

            // 检查插件归属类型：madong(官方)、third(第三方)、custom(自定义)
            $pluginType = $infoConfig['type'] ?? 'madong';

            // 检查是否为madong类型的插件
            if (!str_starts_with($pluginType, 'madong:') && $pluginType !== 'madong') {
                continue;
            }

            $pluginKey            = $infoConfig['name'] ?? $dir;
            $existingPluginKeys[] = $pluginKey;

            // 检查 installed.php 判断是否已安装
            $installedPath = $this->pluginDir . '/' . $dir . '/config/installed.php';
            $isInstalled   = file_exists($installedPath);
            $installedAt  = 0;
            $status       = 0; // 默认未安装

            if ($isInstalled) {
                $installedConfig = include $installedPath;
                if (is_array($installedConfig) && !empty($installedConfig['installed_at'])) {
                    $installedAt = strtotime($installedConfig['installed_at']);
                } else {
                    $installedAt = time();
                }
                $status = 1; // 已安装
            }

            // 检查插件是否已存在于数据库
            $existingPlugin = $this->dao->findByKey($pluginKey);
            if ($existingPlugin) {
                // 如果数据库存在但安装状态不同，同步更新
                if ($existingPlugin->installed_at != $installedAt || $existingPlugin->status != $status) {
                    $existingPlugin->installed_at = $installedAt;
                    $existingPlugin->status      = $status;
                    $existingPlugin->save();
                }
                continue;
            }

            // 读取icon和cover图片（base64或路径）
            $icon  = $this->getPluginImageBase64($dir, 'icon');
            $cover = $this->getPluginImageBase64($dir, 'cover');

            // 使用 info.php 的字段
            $pluginsToInsert[] = [
                'id'           => Snowflake::generate(),
                'title'        => $infoConfig['description'] ?? $pluginKey,
                'key'          => $pluginKey,
                'desc'         => $infoConfig['description'] ?? '',
                'author'       => $infoConfig['author'] ?? '',
                'version'      => $infoConfig['version'] ?? '1.0.0',
                'type'         => $pluginType,
                'icon'         => $icon,
                'cover'        => $cover,
                'status'       => $status,
                'support_app'  => 'admin',
                'created_at'   => time(),
                'updated_at'   => time(),
                'installed_at' => $installedAt,
            ];
        }
        // 批量插入新插件
        if (!empty($pluginsToInsert)) {
            $this->dao->batchInsert($pluginsToInsert);
        }
        // 移除数据库中已不存在的插件
        $this->removeNonExistingPlugins($existingPluginKeys);
    }

    /**
     * 移除数据库中已不存在的插件
     *
     * @param array $existingPluginKeys 现有的插件key列表
     *
     * @return void
     * @throws \Exception
     */
    private function removeNonExistingPlugins(array $existingPluginKeys): void
    {
        $allDbPluginKeys = $this->dao->getAllKeys();
        $keysToDelete    = array_diff($allDbPluginKeys, $existingPluginKeys);

        if (!empty($keysToDelete)) {
            // 批量删除不存在的插件
            foreach ($keysToDelete as $key) {
                $plugin = $this->dao->findByKey($key);
                if ($plugin) {
                    $plugin->delete();
                }
            }
        }
    }

    /**
     * 获取插件图片的base64编码
     *
     * @param string $pluginName 插件名称
     * @param string $imageName  图片名称（icon或cover）
     *
     * @return string|null
     */
    private function getPluginImageBase64(string $pluginName, string $imageName): ?string
    {
        $imagePath = $this->pluginDir . '/' . $pluginName . '/public/' . $imageName . '.png';
        if (!file_exists($imagePath)) {
            // 尝试查找resource目录下的图片
            $resourcePath = $this->pluginDir . '/' . $pluginName . '/resource/public/' . $imageName . '.png';
            if (!file_exists($resourcePath)) {
                return null;
            }
            $imagePath = $resourcePath;
        }
        $imageData = file_get_contents($imagePath);
        if ($imageData === false) {
            return null;
        }
        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * 获取插件详情
     *
     * @param string|int $id 插件ID
     *
     * @return Plugin|null
     * @throws \Exception
     */
    public function read(string|int $id): ?Plugin
    {
        $plugin = $this->dao->get($id);
        if (!$plugin) {
            return null;
        }
        $pluginConfigPath = $this->pluginDir . '/' . $plugin->key . '/config/app.php';
        if (file_exists($pluginConfigPath)) {
            $config = include $pluginConfigPath;
            if (is_array($config)) {
                // 更新插件信息
                $plugin->title   = $config['title'] ?? $plugin->title;
                $plugin->desc    = $config['description'] ?? $plugin->desc;
                $plugin->author  = $config['author'] ?? $plugin->author;
                $plugin->version = $config['version'] ?? $plugin->version;
                $plugin->type    = $config['type'] ?? $plugin->type;
                $plugin->icon    = $this->getPluginImageBase64($plugin->key, 'icon');
                $plugin->cover   = $this->getPluginImageBase64($plugin->key, 'cover');
            }
        }
        return $plugin;
    }

    /**
     * 创建插件
     *
     * @param array $data
     *
     * @return void
     * @throws \Throwable
     * @throws \core\exception\handler\AdminException
     */
    public function store(array $data): void
    {
        $this->transaction(function () use ($data) {
            try {
                if (empty($data['icon'])) {
                    $data['icon'] = $this->generateDefaultIcon($data['title'] ?? 'Plugin');
                }
                if (empty($data['cover'])) {
                    $data['cover'] = $this->generateDefaultCover($data['title'] ?? 'Plugin');
                }
                $model = $this->dao->save($data);
                if (empty($model)) {
                    throw new AdminException('插件创建失败');
                }

                $monitor_support_pause = method_exists(Monitor::class, 'pause');
                if ($monitor_support_pause) {
                    Monitor::pause();
                }
                //生成插件模板
                /**@var \app\service\core\plugin\PluginDevelopService $service */
                $service           = Container::make(\app\service\core\plugin\PluginDevelopService::class);
                $pluginName        = $model->key;
                $pluginTitle       = $model->title;
                $pluginDescription = $model->desc;
                $frontendType      = $data['frontend_type'] ?? 'admin';
                $result            = $service->generatePluginTemplate($pluginName, $pluginTitle, $pluginDescription, $frontendType);
                if ($result['code'] !== 200) {
                    throw new AdminException($result['message']);
                }

                // 生成插件 info.php 配置文件
                $this->createPluginInfoConfig($pluginName, $data);

                //添加图片到对应插件位置
                $publicPath = $this->pluginDir . '/' . $pluginName . '/public';
                // 确保public目录存在
                if (!is_dir($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }
                $iconPath  = $publicPath . '/icon.png';
                $coverPath = $publicPath . '/cover.png';
                $this->saveBase64Image($data['icon'], $iconPath);
                $this->saveBase64Image($data['cover'], $coverPath);
                if ($monitor_support_pause) {
                    Monitor::resume();
                }
            } catch (\Exception $e) {
                throw new AdminException($e->getMessage());
            }
        });
    }

    /**
     * 打包插件
     *
     * @param string  $pluginKey      插件标识
     * @param bool    $updateTemplate 是否更新前端模板到resource/template目录（默认true）
     *
     * @return array
     * @throws \core\exception\handler\AdminException
     */
    public function buildPlugin(string $pluginKey, bool $updateTemplate = true): array
    {
        try {
            // 查询插件信息
            $plugin = $this->dao->findByKey($pluginKey);
            if (!$plugin) {
                throw new AdminException('插件不存在');
            }

            // 定义路径
            $backendPath  = $this->pluginDir . '/' . $pluginKey;
            $adminPath    = $this->adminDir . '/' . $pluginKey;
            $webPath      = $this->webDir . '/' . $pluginKey;
            $resourcePath = $backendPath . '/resource/template';

            // 验证后端插件目录是否存在
            if (!is_dir($backendPath)) {
                throw new AdminException("插件后端目录不存在: {$backendPath}");
            }

            // 是否更新前端模板到 resource/template 目录
            if ($updateTemplate) {
                // 清理并创建template目录
                if (is_dir($resourcePath)) {
                    $this->removeDirectory($resourcePath);
                }

                $resourceCount = 0;

                // 复制后台前端到template目录（如果存在）
                if (is_dir($adminPath)) {
                    $this->copyDirectory($adminPath, $resourcePath . '/admin');
                    $resourceCount++;
                }

                // 复制前台前端到resource目录（如果存在）
                if (is_dir($webPath)) {
                    $this->copyDirectory($webPath, $resourcePath . '/web');
                    $resourceCount++;
                }

                if ($resourceCount === 0) {
                    // 没有前端资源，清空resource目录
                    if (is_dir($resourcePath)) {
                        rmdir($resourcePath);
                    }
                }
            } else {
                // 不更新时检查是否已有模板
                $resourceCount = (is_dir($resourcePath . '/admin') || is_dir($resourcePath . '/web')) ? 1 : 0;
            }

            // 调用核心服务的 build 方法打包插件
            $pluginsDir = base_path() . '/runtime/plugins';
            /** @var \app\service\core\plugin\PluginDevelopService $coreService */
            $coreService = Container::make(\app\service\core\plugin\PluginDevelopService::class);
            $result = $coreService->build($pluginKey, $pluginsDir);

            if ($result['code'] !== 200) {
                throw new AdminException($result['message']);
            }

            return [
                'plugin_key'   => $pluginKey,
                'zip_path'     => $result['data']['zip_file_path'],
                'has_frontend' => $resourceCount > 0,
            ];
        } catch (\Exception $e) {
            throw new AdminException('插件打包失败: ' . $e->getMessage());
        }
    }

    /**
     * 复制目录
     *
     * @param string $source 源目录
     * @param string $target 目标目录
     *
     * @return void
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
     * 删除目录及其内容
     *
     * @param string $dir 目录路径
     *
     * @return void
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * 更新插件信息
     *
     * @param int|string $id   插件ID
     * @param array      $data 更新数据
     *
     * @return void
     * @throws \Throwable
     * @throws \core\exception\handler\AdminException
     */
    public function update(int|string $id, array $data): void
    {
        $this->transaction(function () use ($id, $data) {
            $model = $this->dao->get($id);
            if (!$model) {
                throw new AdminException('插件不存在');
            }

            $pluginKey = $model->getAttribute('key');

            // 更新数据库 - 排除key字段,不允许修改插件标识
            $updateData = $data;
            unset($updateData['key']);
            $model->fill($updateData);
            $model->save();

            // 更新插件配置文件 app.php
            $this->updatePluginConfig($pluginKey, $data);

            // 添加图片到对应插件位置(仅当提供了新的base64图片时)
            if (isset($data['icon']) || isset($data['cover'])) {
                $pluginDirName = $this->findPluginDirectory($pluginKey);
                if ($pluginDirName) {
                    $publicPath = $this->pluginDir . '/' . $pluginDirName . '/public';
                    // 确保public目录存在
                    if (!is_dir($publicPath)) {
                        mkdir($publicPath, 0755, true);
                    }

                    if (!empty($data['icon'])) {
                        $iconPath = $publicPath . '/icon.png';
                        $this->saveBase64Image($data['icon'], $iconPath);
                    }

                    if (!empty($data['cover'])) {
                        $coverPath = $publicPath . '/cover.png';
                        $this->saveBase64Image($data['cover'], $coverPath);
                    }
                }
            }
        });
    }

    /**
     * 根据插件key查找实际的目录名
     *
     * @param string $pluginKey 插件key
     *
     * @return string|null
     */
    private function findPluginDirectory(string $pluginKey): ?string
    {
        if (!is_dir($this->pluginDir)) {
            return null;
        }

        $dirs = scandir($this->pluginDir);
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            // 优先使用 info.php 查找插件key
            $infoPath = $this->pluginDir . '/' . $dir . '/config/info.php';
            if (file_exists($infoPath)) {
                $infoConfig = include $infoPath;
                if (is_array($infoConfig) && isset($infoConfig['name']) && $infoConfig['name'] === $pluginKey) {
                    return $dir;
                }
            }

            // 如果 info.php 不存在或找不到，尝试从 app.php 查找（旧版本兼容）
            $configPath = $this->pluginDir . '/' . $dir . '/config/app.php';
            if (!file_exists($configPath)) {
                continue;
            }

            $config = include $configPath;
            if (!is_array($config)) {
                continue;
            }

            if (($config['name'] ?? '') === $pluginKey) {
                return $dir;
            }
        }

        return null;
    }

    /**
     * 更新插件配置文件
     *
     * @param string $pluginKey 插件标识
     * @param array  $data      更新数据
     *
     * @return void
     * @throws \core\exception\handler\AdminException
     */
    private function updatePluginConfig(string $pluginKey, array $data): void
    {
        // 查找实际的插件目录名
        $pluginDirName = $this->findPluginDirectory($pluginKey);
        if (!$pluginDirName) {
            throw new AdminException('插件目录不存在: ' . $pluginKey);
        }
        $configPath = $this->pluginDir . '/' . $pluginDirName . '/config/app.php';
        // 检查配置文件是否存在
        if (!file_exists($configPath)) {
            throw new AdminException('插件配置文件不存在: ' . $configPath);
        }
        // 加载当前配置
        $config = include $configPath;
        if (!is_array($config)) {
            throw new AdminException('插件配置文件格式错误');
        }
        // 更新配置项
        $fieldMapping = [
            'title'   => 'title',
            'desc'    => 'description',
            'author'  => 'author',
            'version' => 'version',
            'type'    => 'type',
            'status'  => 'enable',
        ];

        foreach ($fieldMapping as $dbField => $configField) {
            if (isset($data[$dbField])) {
                if ($dbField === 'status') {
                    // status字段转换为enable布尔值
                    $config[$configField] = (bool)$data[$dbField];
                } else {
                    $config[$configField] = $data[$dbField];
                }
            }
        }

        // 生成PHP配置文件内容
        $phpContent = "<?php\n\nreturn " . $this->arrayToPhpCode($config) . ";\n";

        // 写入配置文件
        file_put_contents($configPath, $phpContent);

        // 同时更新 info.php 配置文件
        $this->updatePluginInfoConfig($pluginDirName, $data);
    }

    /**
     * 创建插件 info.php 配置文件
     *
     * @param string $pluginKey 插件标识
     * @param array  $data      插件数据
     *
     * @return void
     * @throws \core\exception\handler\AdminException
     */
    private function createPluginInfoConfig(string $pluginKey, array $data): void
    {
        $pluginDir = $this->pluginDir . '/' . $pluginKey;
        $configDir = $pluginDir . '/config';

        // 确保 config 目录存在
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        $infoConfig = [
            'name'          => $pluginKey,
            'identifier'    => $pluginKey,
            'type'          => $data['type'] ?? 'madong', // 插件归属类型：madong(官方)、third(第三方)、custom(自定义)
            'version'       => $data['version'] ?? '1.0.0',
            'description'   => $data['desc'] ?? '',
            'author'        => $data['author'] ?? '',
            'author_email'  => $data['author_email'] ?? '',
            'website'       => $data['website'] ?? '',
            'uninstall'     => [
                'drop_tables'         => false,
                'remove_dependencies' => false,
            ],
        ];

        $phpContent = "<?php\n\n/**\n * 插件信息配置\n */\n\nreturn " . $this->arrayToPhpCode($infoConfig) . ";\n";
        file_put_contents($configDir . '/info.php', $phpContent);
    }

    /**
     * 更新插件 info.php 配置文件
     *
     * @param string $pluginDirName 插件目录名
     * @param array  $data          更新数据
     *
     * @return void
     * @throws \core\exception\handler\AdminException
     */
    private function updatePluginInfoConfig(string $pluginDirName, array $data): void
    {
        $infoPath = $this->pluginDir . '/' . $pluginDirName . '/config/info.php';

        // 如果 info.php 不存在，则创建
        if (!file_exists($infoPath)) {
            $this->createPluginInfoConfig($pluginDirName, $data);
            return;
        }

        // 加载当前配置
        $infoConfig = include $infoPath;
        if (!is_array($infoConfig)) {
            $infoConfig = [];
        }

        // 更新配置项
        $fieldMapping = [
            'type'          => 'type',
            'version'       => 'version',
            'desc'          => 'description',
            'author'        => 'author',
            'author_email'  => 'author_email',
            'website'       => 'website',
        ];

        foreach ($fieldMapping as $dbField => $infoField) {
            if (isset($data[$dbField])) {
                $infoConfig[$infoField] = $data[$dbField];
            }
        }

        // 确保必要的字段存在
        if (!isset($infoConfig['name'])) {
            $infoConfig['name'] = $pluginDirName;
        }
        if (!isset($infoConfig['identifier'])) {
            $infoConfig['identifier'] = $pluginDirName;
        }
        if (!isset($infoConfig['type'])) {
            $infoConfig['type'] = 'madong'; // 默认归属 madong
        }
        if (!isset($infoConfig['uninstall'])) {
            $infoConfig['uninstall'] = [
                'drop_tables'         => false,
                'remove_dependencies' => false,
            ];
        }

        // 生成PHP配置文件内容
        $phpContent = "<?php\n\n/**\n * 插件信息配置\n */\n\nreturn " . $this->arrayToPhpCode($infoConfig) . ";\n";

        // 写入配置文件
        file_put_contents($infoPath, $phpContent);
    }

    /**
     * 将数组转换为PHP代码格式
     *
     * @param array $array 数组
     * @param int   $level 缩进级别
     *
     * @return string
     */
    private function arrayToPhpCode(array $array, int $level = 1): string
    {
        $indent     = str_repeat('    ', $level);
        $nextIndent = str_repeat('    ', $level + 1);

        $result = "[\n";

        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $result .= $nextIndent;
            } else {
                $result .= $nextIndent . "'{$key}' => ";
            }

            if (is_array($value)) {
                $result .= $this->arrayToPhpCode($value, $level + 1);
            } elseif (is_bool($value)) {
                $result .= $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $result .= 'null';
            } elseif (is_string($value)) {
                // 转义单引号
                $escaped = str_replace("'", "\\'", $value);
                $result  .= "'{$escaped}'";
            } else {
                $result .= $value;
            }

            $result .= ",\n";
        }

        $result .= $indent . ']';

        return $result;
    }

    /**
     * 保存base64图片到文件
     *
     * @param string $base64     base64编码的图片
     * @param string $targetPath 目标路径
     *
     * @return void
     */
    private function saveBase64Image(string $base64, string $targetPath): void
    {
        // 提取base64数据
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $imageData = substr($base64, strpos($base64, ',') + 1);
            $imageData = base64_decode($imageData);
            if ($imageData !== false) {
                file_put_contents($targetPath, $imageData);
            }
        }
    }

    /**
     * 生成默认图标（64x64）
     *
     * @param string $text 文字
     *
     * @return string base64编码的图片
     */
    private function generateDefaultIcon(string $text): string
    {
        // 提取首字母
        $firstChar = mb_substr($text, 0, 1, 'UTF-8');

        // 创建64x64的图像
        $size  = 64;
        $image = imagecreatetruecolor($size, $size);

        // 分配颜色
        $bgColor   = imagecolorallocate($image, 64, 158, 255); // 蓝色背景 #409EFF
        $textColor = imagecolorallocate($image, 255, 255, 255); // 白色文字

        // 填充背景
        imagefill($image, 0, 0, $bgColor);

        // 绘制圆角矩形
        $radius  = 12;
        $corners = [
            [0, 0], [$size - 1, 0],
            [0, $size - 1], [$size - 1, $size - 1],
        ];

        foreach ($corners as $corner) {
            imagefilledarc($image, $corner[0] + $radius, $corner[1] + $radius, $radius * 2, $radius * 2, 0, 360, $bgColor, IMG_ARC_PIE);
        }

        // 添加文字
        $fontSize = 24;
        $fontFile = $this->getFontFile();
        if ($fontFile && file_exists($fontFile)) {
            $bbox       = imagettfbbox($fontSize, 0, $fontFile, $firstChar);
            $textWidth  = $bbox[2] - $bbox[0];
            $textHeight = $bbox[7] - $bbox[1];
            $x          = ($size - $textWidth) / 2 - $bbox[0];
            $y          = ($size - $textHeight) / 2 - $bbox[1];
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFile, $firstChar);
        } else {
            // 如果没有字体文件，使用内置字体
            imagestring($image, 5, ($size - imagefontwidth(5)) / 2, ($size - imagefontheight(5)) / 2, $firstChar, $textColor);
        }

        // 转换为base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * 生成默认封面（200x120）
     *
     * @param string $text 文字
     *
     * @return string base64编码的图片
     */
    private function generateDefaultCover(string $text): string
    {
        // 创建200x120的图像
        $width  = 200;
        $height = 120;
        $image  = imagecreatetruecolor($width, $height);

        // 分配颜色
        $bgColor   = imagecolorallocate($image, 240, 242, 245); // 浅灰色背景 #F0F2F5
        $lineColor = imagecolorallocate($image, 220, 223, 230); // 线条颜色 #DCDFE6
        $textColor = imagecolorallocate($image, 144, 147, 153); // 文字颜色 #909399

        // 填充背景
        imagefill($image, 0, 0, $bgColor);

        // 绘制圆角
        $radius  = 8;
        $corners = [
            [0, 0], [$width - 1, 0],
            [0, $height - 1], [$width - 1, $height - 1],
        ];

        foreach ($corners as $corner) {
            imagefilledarc($image, $corner[0] + $radius, $corner[1] + $radius, $radius * 2, $radius * 2, 0, 360, $bgColor, IMG_ARC_PIE);
        }

        // 绘制虚线边框
        imagesetthickness($image, 1);
        imagerectangle($image, 1, 1, $width - 2, $height - 2, $lineColor);

        // 绘制中间的+号
        $plusSize = 24;
        $centerX  = $width / 2;
        $centerY  = $height / 2;

        imagesetthickness($image, 2);
        imageline($image, $centerX - $plusSize, $centerY, $centerX + $plusSize, $centerY, $textColor);
        imageline($image, $centerX, $centerY - $plusSize, $centerX, $centerY + $plusSize, $textColor);

        // 转换为base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * 获取字体文件路径
     *
     * @return string|null
     */
    private function getFontFile(): ?string
    {
        $fontPaths = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', // Linux
            'C:/Windows/Fonts/msyh.ttc', // Windows 微软雅黑
            'C:/Windows/Fonts/simhei.ttf', // Windows 黑体
            '/System/Library/Fonts/PingFang.ttc', // macOS
        ];

        foreach ($fontPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

}
