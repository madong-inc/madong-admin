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

namespace app\command\install;

use app\command\BaseCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 下载前端代码命令
 * 使用方法：
 * - 下载所有前端代码：php webman madong-download:frontend
 * - 下载指定代码：php webman madong-download:frontend --admin --web
 * - 指定分支：php webman madong-download:frontend -b develop
 * - 强制更新：php webman madong-download:frontend -f
 *
 * @author Mr.April
 * @since 1.0.0
 */
#[AsCommand(
    name: 'madong-download:frontend',
    description: 'Download frontend code to the root directory',
    aliases: ['madong-download:frontend'],
    hidden: false
)]
class DownloadCommand extends BaseCommand
{
    // 前端配置信息 - 支持灵活配置下载项目和重命名
    // 所有前端代码统一下载到 frontend 目录下
    private array $frontendConfigs = [
        'admin' => [
            'name'     => '后台下载',
            'git_url'  => 'https://gitee.com/motion-code/madong-vue.git',
            'dir_name' => 'frontend' . DIRECTORY_SEPARATOR . 'admin',
        ],
        'web' => [
            'name'     => '前台下载',
            'git_url'  => 'https://gitee.com/motion-code/madong-nuxt.git',
            'dir_name' => 'frontend' . DIRECTORY_SEPARATOR . 'web',
        ],
    ];

    /**
     * 配置命令
     */
    protected function configure(): void
    {
        $this
            ->addOption('admin', 'a', InputOption::VALUE_NONE, 'Download admin frontend code')
            ->addOption('web', 'w', InputOption::VALUE_NONE, 'Download web frontend code')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force download (overwrite existing files)')
            ->addOption('branch', 'b', InputOption::VALUE_REQUIRED, 'Specify git branch to download', 'main');

        // 为配置的每个项目自动添加选项
        foreach (array_keys($this->frontendConfigs) as $project) {
            if (!in_array($project, ['admin', 'web'])) { // 跳过已经手动添加的选项
                $this->addOption($project, null, InputOption::VALUE_NONE, "Download {$project} frontend code");
            }
        }
    }

    /**
     * 执行命令
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Frontend Code Downloader');

        // 验证Git是否可用
        if (!$this->isGitAvailable()) {
            $io->error('Git is not available on your system. Please install Git first.');
            $io->note('You can download Git from https://git-scm.com/downloads');
            return $this->outputError($io, 'Git is not available');
        }

        // 获取参数
        $forceDownload = $input->getOption('force');
        $branch = $input->getOption('branch');

        // 确定要下载的项目列表
        $projectsToDownload = [];
        $anyOptionSelected = false;

        foreach (array_keys($this->frontendConfigs) as $project) {
            if ($input->getOption($project)) {
                $projectsToDownload[] = $project;
                $anyOptionSelected = true;
            }
        }

        // 如果没有指定具体项目，默认下载所有
        if (!$anyOptionSelected) {
            $projectsToDownload = array_keys($this->frontendConfigs);
        }

        // 获取当前项目根目录
        $rootDir = dirname(base_path());
        $io->info("Project root directory: {$rootDir}");

        // 确保 frontend 目录存在
        $frontendDir = $rootDir . DIRECTORY_SEPARATOR . 'frontend';
        if (!is_dir($frontendDir)) {
            if (!mkdir($frontendDir, 0755, true)) {
                $io->error("Failed to create frontend directory");
                return $this->outputError($io, 'Failed to create frontend directory');
            }
            $io->info("Created frontend directory");
        }

        $io->info("Using git mode for downloading code");

        // 初始化统计信息
        $successCount = 0;
        $totalCount = count($projectsToDownload);

        // 逐个下载前端项目
        foreach ($projectsToDownload as $project) {
            $frontendConfig = $this->frontendConfigs[$project];
            $io->section("Downloading: {$frontendConfig['name']}");

            // 定义前端代码保存的目录和git地址
            $frontendGitUrl = $frontendConfig['git_url'];
            $frontendDir = $frontendConfig['dir_name'];
            $frontendFullPath = $rootDir . DIRECTORY_SEPARATOR . $frontendDir;

            // 检查前端代码目录是否已存在
            if (is_dir($frontendFullPath)) {
                if (!$forceDownload) {
                    if (!$io->confirm("Directory '{$frontendDir}' already exists. Do you want to update it?", true)) {
                        $io->note("Skipping update for {$frontendDir} directory");
                        continue;
                    }
                }

                $io->info("Directory exists, updating {$frontendDir} code...");

                // 执行git pull命令
                $command = "cd \"{$frontendFullPath}\" && git pull origin {$branch}";
                $io->text("Executing: {$command}");

                $outputBuffer = [];
                $returnValue = 0;
                exec($command, $outputBuffer, $returnValue);

                // 输出结果
                foreach ($outputBuffer as $line) {
                    $io->text($line);
                }

                if ($returnValue !== 0) {
                    $io->error("Failed to update {$frontendDir}");
                    continue;
                }
            } else {
                $io->info("Directory does not exist, cloning {$frontendDir} code...");

                // 执行git clone命令 - 自动重命名为配置的目录名
                $command = "git clone -b {$branch} {$frontendGitUrl} {$frontendDir}";
                $io->text("Executing: {$command}");

                $outputBuffer = [];
                $returnValue = 0;
                exec("cd \"{$rootDir}\" && " . $command, $outputBuffer, $returnValue);

                // 输出结果
                foreach ($outputBuffer as $line) {
                    $io->text($line);
                }

                if ($returnValue !== 0) {
                    $io->error("Failed to clone {$frontendDir}");
                    continue;
                }
            }

            $io->success("Successfully downloaded {$frontendDir} frontend code");
            $io->note("Saved to: {$frontendFullPath}");
            $successCount++;
        }

        // 输出总结
        $io->section('Download Summary');
        if ($successCount === $totalCount) {
            $io->success("Successfully downloaded all {$successCount} frontend code!");
            return Command::SUCCESS;
        } elseif ($successCount > 0) {
            $io->warning("Downloaded {$successCount} of {$totalCount} frontend code successfully");
            return Command::FAILURE;
        } else {
            $io->error("Failed to download any frontend code");
            return Command::FAILURE;
        }
    }

    /**
     * 检查Git是否可用
     */
    private function isGitAvailable(): bool
    {
        $output = [];
        $returnValue = 0;
        exec('git --version', $output, $returnValue);
        return $returnValue === 0;
    }
}