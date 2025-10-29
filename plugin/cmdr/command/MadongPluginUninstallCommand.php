<?php

namespace plugin\cmdr\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Webman\Console\Util;

class MadongPluginUninstallCommand extends Command
{
    protected static string $defaultName = 'madong-plugins:uninstall';
    protected static string $defaultDescription = 'App Plugins Uninstall';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'App plugins name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("Uninstall App Plugin $name");
        $class = "\\madong\\plugins\\$name\\api\\Install";
        if (!method_exists($class, 'uninstall')) {
            throw new \RuntimeException("Method $class::uninstall not exists");
        }
        call_user_func([$class, 'uninstall'], config("madong.plugins.$name.app.version"));
        return self::SUCCESS;
    }

}
