<?php

namespace core\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Webman\Console\Util;

/**
 * 生成中间件
 *
 * @author Mr.April
 * @since  1.0
 */
class MarkMiddlewareCommand extends Command
{
    protected static string $defaultName = 'madong-mark:middleware';
    protected static string $defaultDescription = 'Gen middleware';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Middleware name');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("Gen middleware $name");

        $name = str_replace('\\', '/', $name);
        if (!$middleware_str = Util::guessPath(app_path(), 'middleware')) {
            $middleware_str = Util::guessPath(app_path(), 'controller') === 'Controller' ? 'Middleware' : 'middleware';
        }
        $upper = $middleware_str === 'Middleware';
        if (!($pos = strrpos($name, '/'))) {
            $name      = ucfirst($name);
            $file      = app_path() . DIRECTORY_SEPARATOR . $middleware_str . DIRECTORY_SEPARATOR . "$name.php";
            $namespace = $upper ? 'App\Middleware' : 'app\middleware';
        } else {
            if ($real_name = Util::guessPath(app_path(), $name)) {
                $name = $real_name;
            }
            if ($upper && !$real_name) {
                $name = preg_replace_callback('/\/([a-z])/', function ($matches) {
                    return '/' . strtoupper($matches[1]);
                }, ucfirst($name));
            }
            $path      = "$middleware_str/" . substr($upper ? ucfirst($name) : $name, 0, $pos);
            $name      = ucfirst(substr($name, $pos + 1));
            $file      = app_path() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "$name.php";
            $namespace = str_replace('/', '\\', ($upper ? 'App/' : 'app/') . $path);
        }

        if (is_file($file)) {
            $helper   = $this->getHelper('question');
            $question = new ConfirmationQuestion("$file already exists. Do you want to override it? (yes/no)", false);
            if (!$helper->ask($input, $output, $question)) {
                return Command::SUCCESS;
            }
        }

        $this->createMiddleware($name, $namespace, $file);

        return self::SUCCESS;
    }

    /**
     * @param $name
     * @param $namespace
     * @param $file
     *
     * @return void
     */
    protected function createMiddleware($name, $namespace, $file): void
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $middleware_content = <<<EOF
<?php
namespace $namespace;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class $name implements MiddlewareInterface
{
    public function process(Request \$request, callable \$handler) : Response
    {
        return \$handler(\$request);
    }
    
}

EOF;
        file_put_contents($file, $middleware_content);
    }

}
