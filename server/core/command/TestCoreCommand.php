<?php
declare(strict_types=1);

namespace core\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Webman\Console\Util;

class TestCoreCommand extends Command
{

    protected static string $defaultName = 'test:core';
    protected static string $defaultDescription = '这是一个应用命令（来自 core/command）';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::$defaultName)->setDescription(static::$defaultDescription);
        $this->addArgument('name', InputArgument::REQUIRED, 'App plugin name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('<info>执行成功：应用命令 test:core</info>');
        return self::SUCCESS;
    }

}
