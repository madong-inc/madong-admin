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
use app\service\core\plugin\PluginUninstallService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 插件删除命令
 * 使用方法：
 * - 删除插件：php webman madong-plugin:delete plugin-name
 * - 移除插件（别名）：php webman madong-plugin:remove plugin-name
 *
 * 注意：删除前必须先卸载插件
 *
 * @author Mr.April
 * @since 1.0.0
 */
#[AsCommand(
    name: 'madong-plugin:delete',
    description: 'Delete plugin package from the system (requires plugin to be uninstalled first)',
    aliases: ['madong-plugin:delete', 'madong-plugin:remove'],
    hidden: false
)]
class DeleteCommand extends BaseCommand
{
    /**
     * 配置命令
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Plugin name');
    }

    /**
     * 执行命令
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Plugin Deleter');

        // 获取参数
        $name = $input->getArgument('name');

        $io->info("Deleting plugin: {$name}");
        $io->note("Note: Plugin must be uninstalled before deletion");

        // 确认删除
        if (!$this->confirm($io, "Are you sure you want to delete plugin {$name}? This action cannot be undone.")) {
            $io->note("Deletion cancelled");
            return Command::SUCCESS;
        }

        try {
            // 创建删除服务实例（统一入口）
            $deleteService = new PluginUninstallService();

            // 执行删除流程
            $result = $deleteService->delete($name);

            if ($result) {
                return $this->outputSuccess($io, "Plugin '{$name}' deleted successfully!");
            } else {
                return $this->outputError($io, "Plugin '{$name}' deletion failed!");
            }

        } catch (\Exception $e) {
            return $this->outputError($io, sprintf("Deletion failed: %s", $e->getMessage()), $e);
        }
    }
}

