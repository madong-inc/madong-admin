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

namespace app\command\plugin;

use app\command\BaseCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 删除插件命令
 *
 * @author Mr.April
 * @since 1.0.0
 */
#[AsCommand(
    name: 'madong-plugin:develop:delete',
    description: 'Delete plugin',
    aliases: ['madong-plugin:dev:delete'],
    hidden: false
)]
class DevelopDeleteCommand extends BaseCommand
{
    /**
     * 配置命令
     */
    protected function configure(): void
    {
        $this->addArgument('key', InputArgument::REQUIRED, 'Plugin key (kebab-case format, e.g. test-demo)');
    }

    /**
     * 执行命令
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Delete Plugin');

        // 获取参数
        $pluginKey = $input->getArgument('key');
        $pluginName = str_replace('-', '_', $pluginKey);

        $io->info(sprintf("Deleting plugin: %s", $pluginKey));

        try {
            // 使用 base_path() 计算路径
            $basePath = base_path();
            $projectPath = dirname($basePath);

            // 定义路径
            $frontendPath = $projectPath . '/admin/src/apps/' . $pluginName;
            $backendPath = $basePath . '/plugin/' . $pluginName;
            $runtimePath = $basePath . '/runtime/plugins/' . $pluginKey;
            $runtimeZipPath = $basePath . '/runtime/plugins/' . $pluginKey . '.zip';
            $buildPath = $basePath . '/runtime/build/' . $pluginKey;

            // 删除前端目录
            if (is_dir($frontendPath)) {
                $this->deleteDirectory($frontendPath);
                $io->text(sprintf("Deleted frontend directory: %s", $frontendPath));
            }

            // 删除后端目录
            if (is_dir($backendPath)) {
                $this->deleteDirectory($backendPath);
                $io->text(sprintf("Deleted backend directory: %s", $backendPath));
            }

            // 删除构建目录
            if (is_dir($buildPath)) {
                $this->deleteDirectory($buildPath);
                $io->text(sprintf("Deleted build directory: %s", $buildPath));
            }

            // 删除运行时目录
            if (is_dir($runtimePath)) {
                $this->deleteDirectory($runtimePath);
                $io->text(sprintf("Deleted runtime directory: %s", $runtimePath));
            }

            // 删除运行时ZIP文件
            if (file_exists($runtimeZipPath)) {
                unlink($runtimeZipPath);
                $io->text(sprintf("Deleted runtime ZIP file: %s", $runtimeZipPath));
            }

            return $this->outputSuccess($io, "Plugin deleted successfully!");
        } catch (\Exception $e) {
            return $this->outputError($io, sprintf("Deletion failed: %s", $e->getMessage()), $e);
        }
    }
}
