<?php

namespace core\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Webman\Console\Util;

/**
 * 生成Dao
 *
 * @author Mr.April
 * @since  1.0
 */
class MarkDaoCommand extends Command
{
    protected static string $defaultName = 'madong-mark:dao';
    protected static string $defaultDescription = 'Gen dao';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Dao name');
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

        $suffix = config('app.dao_suffix', 'Dao');

        if ($suffix && !strpos($name, $suffix)) {
            $name .= $suffix;
        }

        $name = str_replace('\\', '/', $name);
        if (!($pos = strrpos($name, '/'))) {
            $name           = ucfirst($name);
            $controller_str = Util::guessPath(app_path(), 'Dao') ?: 'dao';
            $file           = app_path() . DIRECTORY_SEPARATOR . $controller_str . DIRECTORY_SEPARATOR . "$name.php";
            $namespace      = $controller_str === 'Dao' ? 'App\dao' : 'app\dao';
        } else {
            $name_str = substr($name, 0, $pos);
            if ($real_name_str = Util::guessPath(app_path(), $name_str)) {
                $name_str = $real_name_str;
            } else if ($real_section_name = Util::guessPath(app_path(), strstr($name_str, '/', true))) {
                $upper = strtolower($real_section_name[0]) !== $real_section_name[0];
            } else if ($real_base_controller = Util::guessPath(app_path(), 'dao')) {
                $upper = strtolower($real_base_controller[0]) !== $real_base_controller[0];
            }
            $upper = $upper ?? strtolower($name_str[0]) !== $name_str[0];
            if ($upper && !$real_name_str) {
                $name_str = preg_replace_callback('/\/([a-z])/', function ($matches) {
                    return '/' . strtoupper($matches[1]);
                }, ucfirst($name_str));
            }
            $path      = "$name_str/" . ($upper ? 'Dao' : 'dao');
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

        $this->createDao($name, $namespace, $file);

        return self::SUCCESS;
    }

    /**
     * @param $name
     * @param $namespace
     * @param $file
     *
     * @return void
     */
    protected function createDao($name, $namespace, $file): void
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // 后缀配置
        $daoSuffix = config('app.dao_suffix', 'Dao');

        // 移除名称中的DAO后缀
        $cleanName = $this->removeSuffix($name, $daoSuffix);

        $dao_content = <<<EOF
<?php

namespace $namespace;

use core\abstract\BaseDao;

class $name extends BaseDao
{
     protected function setModel(): string
    {
        return $cleanName::class;
    }

}

EOF;
        file_put_contents($file, $dao_content);
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
