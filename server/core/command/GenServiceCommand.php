<?php

namespace core\command;

use support\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Webman\Console\Util;

/**
 * 生成服务层
 *
 * @author Mr.April
 * @since  1.0
 */
class GenServiceCommand extends Command
{
    protected static string $defaultName = 'gen:service';
    protected static string $defaultDescription = 'Gen service';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Service name');
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
        $output->writeln("Gen dao $name");

        $suffix = config('app.service_suffix', 'Service');

        if ($suffix && !strpos($name, $suffix)) {
            $name .= $suffix;
        }

        $name = str_replace('\\', '/', $name);
        if (!($pos = strrpos($name, '/'))) {
            $name           = ucfirst($name);
            $controller_str = Util::guessPath(app_path(), 'Service') ?: 'service';
            $file           = app_path() . DIRECTORY_SEPARATOR . $controller_str . DIRECTORY_SEPARATOR . "$name.php";
            $namespace      = $controller_str === 'Service' ? 'App\service' : 'app\service';
        } else {
            $name_str = substr($name, 0, $pos);
            if ($real_name_str = Util::guessPath(app_path(), $name_str)) {
                $name_str = $real_name_str;
            } else if ($real_section_name = Util::guessPath(app_path(), strstr($name_str, '/', true))) {
                $upper = strtolower($real_section_name[0]) !== $real_section_name[0];
            } else if ($real_base_controller = Util::guessPath(app_path(), 'service')) {
                $upper = strtolower($real_base_controller[0]) !== $real_base_controller[0];
            }
            $upper = $upper ?? strtolower($name_str[0]) !== $name_str[0];
            if ($upper && !$real_name_str) {
                $name_str = preg_replace_callback('/\/([a-z])/', function ($matches) {
                    return '/' . strtoupper($matches[1]);
                }, ucfirst($name_str));
            }
            $path      = "$name_str/" . ($upper ? 'Service' : 'service');
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

        $this->createService($name, $namespace, $file);

        return self::SUCCESS;
    }

    /**
     * @param $name
     * @param $namespace
     * @param $file
     *
     * @return void
     */
    protected function createService($name, $namespace, $file): void
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // Class Name
        $daoFullClassStr = 'Your Dao Class Name';

        $service_content = <<<EOF
<?php

namespace $namespace;

use core\abstract\BaseService;
use support\Container;

class $name extends BaseService
{

    public function __construct()
    {
         // 初始化Dao实例（使用完全限定类名的::class常量）
        \$this->dao = Container::make('{$daoFullClassStr}');
    }


}

EOF;
        file_put_contents($file, $service_content);
    }

    /**
     * 从名称末尾移除指定后缀（若存在）
     *
     * @param string $name   原始名称（支持多级目录，如`Api/UserDao`）
     * @param string $suffix 要移除的后缀（如`dao`）
     *
     * @return string 移除后缀后的名称
     */
    protected function removeSuffix(string $name, string $suffix): string
    {
        // 无后缀或名称不以该后缀结尾，直接返回原名称
        if (empty($suffix) || !str_ends_with($name, $suffix)) {
            return $name;
        }
        // 截断末尾的后缀（计算后缀长度，取前面的内容）
        return substr($name, 0, -strlen($suffix));
    }
}
