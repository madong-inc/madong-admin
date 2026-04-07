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
 * Official Website: http://www.madong.tech
 */

namespace app\service\core\plugin;

use core\tool\Sse;
use core\uuid\UUIDGenerator;
use support\Container;

/**
 * 插件卸载服务（统一入口）
 * CLI 和在线卸载都统一调用此服务
 * CLI 模式通过迭代器获取 SSE 事件并解析
 * 在线模式直接返回 Generator 给前端处理
 */
class PluginUninstallService extends PluginBaseService
{
    /**
     * 卸载插件（统一入口）
     * 卸载流程统一使用插件自带的 Install.php
     * PluginUninstallService 只负责流程控制，实际卸载逻辑完全由插件 Install.php 处理
     *
     * @param string $code 插件编码
     *
     * @return \Generator CLI 模式返回数组，在线模式返回 Generator
     * @throws \Exception
     */
    public function uninstall(string $code): \Generator
    {
        $sessionUuid = UUIDGenerator::generate();
        $request     = request();
        if ($request) {
            $sessionUuid = $request->input('uuid', $sessionUuid);
        }

        // 检查插件实体是否存在
        $pluginDir        = $this->plugin_path . DIRECTORY_SEPARATOR . $code;
        $pluginConfigFile = $pluginDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'info.php';

        if (!is_dir($pluginDir) || !is_file($pluginConfigFile)) {
            yield Sse::error('插件不存在，无法卸载', [], $sessionUuid);
            return;
        }

        // 检查插件是否允许卸载
        $pluginConfig = $this->getPluginConfig($code);
        if (isset($pluginConfig['uninstall']['undeletable']) && $pluginConfig['uninstall']['undeletable'] === true) {
            yield Sse::error('该插件为系统内置插件，不允许卸载', [], $sessionUuid);
            return;
        }

        // 检查插件是否提供 Install.php
        $className = "plugin\\{$code}\\Install";
        if (!class_exists($className)) {
            yield Sse::error("插件未提供 Install.php，无法进行卸载", [], $sessionUuid);
            return;
        }

        try {
            // 发送卸载开始事件
            yield Sse::progress('开始卸载插件', 10, ['plugin' => $code], $sessionUuid);

            // 统一调用插件的 Install::uninstall() 方法（这是唯一的卸载入口）
            yield Sse::progress('执行插件卸载逻辑', 30, [], $sessionUuid);
            $version = $pluginConfig['version'] ?? '1.0.0';

            // 实例化并执行插件 Install 类（传入进度回调以支持在线模式反馈）
            $installInstance = new $className();

            // 创建临时输出文件（类似 Terminal 的方式）
            $outputFile = runtime_path(self::RUNTIME_PLUGIN_PATH . '/uninstall_' . $sessionUuid . '.log');
            file_put_contents($outputFile, '');

            // 定义进度回调，将插件的卸载进度实时写入文件
            $callbackCount    = 0;
            $progressCallback = function (string $message, ?int $progress = null) use ($outputFile, &$callbackCount) {
                $callbackCount++;
                $progressStr = $progress !== null ? "(progress: {$progress})" : "(no progress)";
                $logLine     = "[CALLBACK #{$callbackCount}] {$message} {$progressStr}\n";
                // 写入文件（追加模式）
                file_put_contents($outputFile, $logLine, FILE_APPEND);
            };

            // 设置进度回调和在线模式
            $installInstance->setProgressCallback($progressCallback);
            $installInstance->setOnlineMode(true);

            // 开启输出缓冲，捕获 Install::uninstall() 内部的 echo 输出
            ob_start();
            $installInstance->uninstall($version);
            $output = ob_get_clean();

            // 将缓冲区输出也写入临时文件
            if (!empty($output)) {
                file_put_contents($outputFile, $output, FILE_APPEND);
            }

            // 读取文件内容并 yield 所有日志
            $fileContent = file_get_contents($outputFile);
            if (!empty($fileContent)) {
                $lines = array_filter(explode("\n", $fileContent));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        // 使用 Sse::progress 统一输出，不更新进度值
                        yield Sse::progress($line, 30, [], $sessionUuid);
                    }
                }
            }

            // 清理临时文件
            @unlink($outputFile);

            yield Sse::progress('插件卸载方法执行成功', 80, [], $sessionUuid);

            // 删除插件内的安装配置
            yield Sse::progress('删除安装配置', 90, [], $sessionUuid);
            $installedConfigPath = $this->plugin_path . DIRECTORY_SEPARATOR . $code . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'installed.php';
            if (is_file($installedConfigPath)) {
                @unlink($installedConfigPath);
            }

            // 发送卸载完成事件
            yield Sse::completed('插件卸载成功', ['plugin' => $code], $sessionUuid);

        } catch (\Throwable $e) {
            // 发送卸载失败事件
            yield Sse::error('卸载失败：' . $e->getMessage(), [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ], $sessionUuid);
        }
    }

    /**
     * 删除插件（仅删除插件包）
     * 删除条件：
     * 1. 插件必须为已卸载状态（installed.php 不存在）
     * 2. 非官方插件（undeletable != true）
     *
     * @param string $code 插件编码
     *
     * @return void 删除成功返回 true
     * @throws \Exception
     */
    public function delete(string $code): void
    {
        // 1. 检查插件实体是否存在
        $pluginDir        = $this->plugin_path . DIRECTORY_SEPARATOR . $code;
        $pluginConfigFile = $pluginDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'info.php';

        if (!is_dir($pluginDir) || !is_file($pluginConfigFile)) {
            throw new \Exception('插件不存在，无法删除');
        }

        // 2. 检查插件配置
        $pluginConfig = $this->getPluginConfig($code);

        // 3. 检查是否为系统内置插件（不允许删除）
        if (isset($pluginConfig['uninstall']['undeletable']) && $pluginConfig['uninstall']['undeletable'] === true) {
            throw new \Exception('系统内置插件不允许删除');
        }

        // 4. 检查插件安装状态（installed.php 不存在）
        $installedConfigPath = $pluginDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'installed.php';
        $isInstalled         = is_file($installedConfigPath);

        if ($isInstalled) {
            throw new \Exception('插件已安装，请先卸载后再删除');
        }

        // 5. 检查插件是否提供 Install.php
        $className = "plugin\\{$code}\\Install";
        if (!class_exists($className)) {
            throw new \Exception("插件未提供 Install.php，无法进行删除");
        }

        try {
            $version = $pluginConfig['version'] ?? '1.0.0';

            // 实例化并执行插件 Install 类
            $installInstance = new $className();

            // 设置为 CLI 模式（不使用回调）
            $installInstance->setOnlineMode(false);

            // 执行删除
            $installInstance->delete($version);

            // 删除后，需要清理数据库中的插件记录
            $this->deletePluginRecord($code);
        } catch (\Throwable $e) {
            throw new \Exception('删除失败：' . $e->getMessage());
        }
    }

    /**
     * 删除插件数据库记录
     */
    protected function deletePluginRecord(string $code): void
    {
        try {
            /** @var PluginService $service */
            $service = Container::make(PluginService::class);
            $model   = $service->dao->query()->where('key', $code)->first();
            if ($model) {
                $model->delete();
            }
        } catch (\Throwable $e) {

        }
    }
}

