<?php
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

namespace plugin\cmdr\app\service;

use Illuminate\Filesystem\Filesystem;
use madong\admin\utils\Util;
use plugin\cmdr\app\enum\CommandEvent;

/**
 * Terminal 执行终端命令并通过 SSE 实时输出结果。
 *
 * @author Mr.April
 * @since  1.0
 */
class Terminal
{
    /**
     * @var ?Terminal 对象实例
     */
    protected static ?Terminal $instance = null;

    /**
     * @var string 当前执行的命令 $command 的 key
     */
    protected string $commandKey = '';

    /**
     * @var array proc_open 的参数
     */
    protected array $descriptorsSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    /**
     * @var resource|bool proc_open 返回的 resource
     */
    protected $process = false;

    /**
     * @var array proc_open 的管道
     */
    protected array $pipes = [];

    /**
     * @var int proc执行状态:0=未执行,1=执行中,2=执行完毕
     */
    protected int $procStatusMark = 0;

    /**
     * @var array proc执行状态数据
     */
    protected array $procStatusData = [];

    /**
     * @var string 命令在前台的uuid
     */
    protected string $uuid = '';

    /**
     * @var string 扩展信息
     */
    protected string $extend = '';

    /**
     * @var string 命令执行输出文件
     */
    protected string $outputFile = '';

    /**
     * @var string 命令执行实时输出内容
     */
    protected string $outputContent = '';

    /**
     * @var string 自动构建的前端文件的目录
     */
    protected static string $distDir = 'web' . DIRECTORY_SEPARATOR . 'dist';

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 在 Webman 中，请求参数可以通过 Request 对象获取
        $this->uuid   = request()->input('uuid', '');
        $this->extend = request()->input('extend', '');

        // 初始化日志文件
        $outputDir = base_path() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'terminal';

        $this->outputFile = $outputDir . DIRECTORY_SEPARATOR . 'exec.log';
        $filesystem       = new Filesystem();

        if (!is_dir($outputDir)) {
            $filesystem->makeDirectory($outputDir, 0755, true);
        }
        $filesystem->put($this->outputFile, '');

        /**
         * 命令执行结果输出到文件而不是管道
         * 因为输出到管道时有延迟，而文件虽然需要频繁读取和对比内容，但是输出实时的
         */
        $this->descriptorsSpec = [
            0 => ['pipe', 'r'], // 标准输入
            1 => ['file', $this->outputFile, 'w'], // 标准输出
            2 => ['file', $this->outputFile, 'w'], // 标准错误
        ];
    }

    /**
     * 获取命令配置
     *
     * @param string $key 命令key
     *
     * @return array|bool
     */
    public static function getCommand(string $key): bool|array
    {
        if (!$key) {
            return false;
        }

        $commands = config('plugin.cmdr.terminal.commands', []);
        if (stripos($key, '.')) {
            $keyParts = explode('.', $key);
            if (!isset($commands[$keyParts[0]]) || !is_array($commands[$keyParts[0]]) || !isset($commands[$keyParts[0]][$keyParts[1]])) {
                return false;
            }
            $command = $commands[$keyParts[0]][$keyParts[1]];
        } else {
            if (!isset($commands[$key])) {
                return false;
            }
            $command = $commands[$key];
        }

        if (!is_array($command)) {
            $command = [
                'cwd'     => base_path(),
                'command' => $command,
            ];
        } else {
            $command = [
                'cwd'     => $command['cwd'],
                'command' => $command['command'],
            ];
        }

        $command['cwd'] = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $command['cwd']);
        return $command;
    }

    /**
     * 执行命令并通过输出-Generator
     *
     * @param bool $authentication
     *
     * @return \Generator
     * @throws \Throwable
     */
    public function exec(bool $authentication = true): \Generator
    {
        $this->commandKey = request()->input('command');
        $command          = self::getCommand($this->commandKey);
        if (!$command) {
            yield $this->formatSseData('Error: The command was not allowed to be executed', CommandEvent::EXEC_ERROR->value);
            return;
        }

        //默认关闭默认路由统一使用路由授权

        $this->beforeExecution();
        yield $this->formatSseData('> ' . 'Connection succeeded', CommandEvent::LINK_SUCCESS->value);
        //没有开启不允许执行
        if (!config('plugin.cmdr.terminal.enabled', false)) {
            yield $this->formatSseData('The command terminal service has not been enabled.', CommandEvent::DEFAULT->value);
            yield $this->formatSseData('Disable', CommandEvent::EXEC_ERROR->value);
            return;
        }

        yield $this->formatSseData('> ' . $command['command'], false);

        $this->process = proc_open($command['command'], $this->descriptorsSpec, $pipes, $command['cwd']);
        if (!is_resource($this->process)) {
            yield $this->formatSseData('Failed to execute', CommandEvent::EXEC_ERROR->value);
            return;
        }

        $this->outputContent = file_get_contents($this->outputFile);
        while ($this->getProcStatus()) {
            $contents = file_get_contents($this->outputFile);
            if ($contents !== $this->outputContent) {
                $newOutput = substr($contents, strlen($this->outputContent));
                if (str_contains($newOutput, "\n")) {
                    yield $this->formatSseData($newOutput); // 直接 yield
                    $this->outputContent = $contents;
                }
            }
            usleep(500000);
        }

        $exitCode = proc_get_status($this->process)['exitcode'];
        yield $this->formatSseData('exitCode: ' . $exitCode, CommandEvent::DEFAULT->value);
        if ($exitCode === 0) {
            if ($this->successCallback()) {
                yield $this->formatSseData('Command execution succeeded', CommandEvent::EXEC_SUCCESS->value);
            } else {
                yield $this->formatSseData('Error: Callback failed', CommandEvent::EXEC_ERROR->value);
            }
        } else {
            yield $this->formatSseData('Command failed with code: ' . $exitCode, CommandEvent::EXEC_ERROR->value);
        }

        yield $this->formatSseData('Command completed', CommandEvent::EXEC_COMPLETED->value);

        foreach ($pipes as $pipe) fclose($pipe);
        proc_close($this->process);

        if (in_array(explode('.', $this->commandKey)[0], ['cmdr-install', 'composer update', 'cmdr-build'])) {
            Util::reloadWebman();
        }
    }

    /**
     * 格式化 SSE 数据，并支持发送前/发送后回调
     *
     * @param string      $data  原始数据
     * @param string|null $event SSE 事件类型
     *
     * @return string 格式化后的 SSE 字符串
     */
    protected function formatSseData(
        string  $data,
        ?string $event = null
    ): string
    {
        $processedData = $data;
        $dataPayload   = [
            'data'   => self::outputFilter($processedData), // 使用处理后的数据
            'event'  => !empty($event) ? $event : CommandEvent::DEFAULT->value,
            'uuid'   => $this->uuid,
            'extend' => $this->extend,
            'key'    => $this->commandKey,
            'date'   => date('Y-m-d H:i:s', time()),
        ];
        $jsonData      = json_encode($dataPayload, JSON_UNESCAPED_UNICODE);
        if ($jsonData === false) {
            $jsonData = json_encode(['error' => 'JSON encode error'], JSON_UNESCAPED_UNICODE);
        }

        $sseLines = [];
        if (!empty($this->uuid)) {
            $sseLines[] = "id: $this->uuid";
        }
        if (!empty($event)) {
            $sseLines[] = "event: $event";
        }
        $sseLines[] = "data: $jsonData";
        return implode("\n", $sseLines) . "\n\n";
    }

    /**
     * 获取执行状态
     *
     * @throws \Throwable
     */
    public function getProcStatus(): bool
    {
        $this->procStatusData = proc_get_status($this->process);
        if ($this->procStatusData['running']) {
            $this->procStatusMark = 1;
            return true;
        } elseif ($this->procStatusMark === 1) {
            $this->procStatusMark = 2;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 成功后回调
     *
     * @return bool
     * @throws \Throwable
     */
    public function successCallback(): bool
    {
        if (stripos($this->commandKey, '.')) {
            $commandKeyArr = explode('.', $this->commandKey);
            $commandPKey   = $commandKeyArr[0] ?? '';
        } else {
            $commandPKey = $this->commandKey;
        }
        if ($commandPKey == 'cmdr-build') {
            //重新发布后需要移动前端文件
            if (!self::mvDist()) {
                return false;
            }
        }
        //更多拓展插件安装等实现
        return true;
    }

    /**
     * 执行前埋点
     */
    public function beforeExecution(): void
    {
        if ($this->commandKey == 'test.pnpm') {
            @unlink(base_path() . '/plugin/cmdr/public' . DIRECTORY_SEPARATOR . 'npm-install-test' . DIRECTORY_SEPARATOR . 'pnpm-lock.yaml');
        } elseif ($this->commandKey == 'cmdr-install.pnpm') {
            @unlink(dirname(base_path()) . '/web' . DIRECTORY_SEPARATOR . 'pnpm-lock.yaml');
        }
    }

    /**
     * 输出过滤
     *
     * @param string $str
     *
     * @return string
     */
    public static function outputFilter(string $str): string
    {
        $str  = trim($str);
        $preg = '/\[(.*?)m/i';
        $str  = preg_replace($preg, '', $str);
        $str  = str_replace(["\r\n", "\r", "\n"], "\n", $str);
        return mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
    }

    /**
     * 移动构建后的 dist 目录到 public 目录
     *
     * @return bool
     */
    public static function mvDist(): bool
    {
        $filesystem   = new Filesystem();
        $distPath     = dirname(base_path()) . DIRECTORY_SEPARATOR . self::$distDir . DIRECTORY_SEPARATOR;
        $toPublicPath = base_path() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

        // 定义源路径和目标路径
        $sourcePaths = [
            'index.html' => $distPath . 'index.html',
            'assets'     => $distPath . 'assets',
        ];
        $targetPaths = [
            'index.html' => $toPublicPath . 'index.html',
            'assets'     => $toPublicPath . 'assets',
        ];

        // 检查源文件/目录是否存在
        if (!file_exists($sourcePaths['index.html']) || !file_exists($sourcePaths['assets'])) {
            return false;
        }
        try {
            // 删除目标位置已有的文件/目录
            if ($filesystem->exists($targetPaths['index.html'])) {
                $filesystem->delete($targetPaths['index.html']);
            }
            if ($filesystem->exists($targetPaths['assets'])) {
                $filesystem->deleteDirectory($targetPaths['assets']);
            }

            // 复制目录（关键改进！）
            if (!$filesystem->copyDirectory($sourcePaths['assets'], $targetPaths['assets'])) {
                throw new \RuntimeException("Failed to copy assets directory");
            }

            // 复制单个文件（index.html）
            if (!$filesystem->copy($sourcePaths['index.html'], $targetPaths['index.html'])) {
                throw new \RuntimeException("Failed to copy index.html");
            }
            // 清理源目录
//            $filesystem->cleanDirectory($distPath);
            $filesystem->deleteDirectory($distPath); // 删文件 + 删目录
            return true;
        } catch (\Throwable $e) {
            error_log("mvDist failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 更改配置
     *
     * @param array $config
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function changeConfig(array $config = []): bool
    {
        $filesystem        = new Filesystem();
        $oldPackageManager = config('plugin.cmdr.terminal.npm_package_manager', '');

        $newPackageManager = $config['manager'] ?? $oldPackageManager;

        if ($oldPackageManager == $newPackageManager) {
            return true;
        }

        $buildConfigFile = base_path() . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'cmdr' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'terminal.php';
        if (!$filesystem->exists($buildConfigFile)) {
            return false;
        }

        $buildConfigContent = $filesystem->get($buildConfigFile);
        $buildConfigContent = preg_replace(
            "/'npm_package_manager'\s*=>\s*'{$oldPackageManager}'/",
            "'npm_package_manager' => '{$newPackageManager}'",
            $buildConfigContent
        );

        $result = $filesystem->put($buildConfigFile, $buildConfigContent);
        return (bool)$result;
    }

    /**
     * 命令执行
     */
    public static function execute(
        string $command,
        string $cwd
    ): array
    {
        $output     = [];
        $return_var = 0;

        // 设置超时时间(5分钟)
        set_time_limit(300);
        exec("cd $cwd && $command 2>&1", $output, $return_var);
        return [
            'success'   => $return_var === 0,
            'output'    => implode(PHP_EOL, $output),
            'exit_code' => $return_var,
        ];
    }

    /**
     * 检查PNPM是否可用
     */
    public static function checkPnpm()
    {
        $cwd    = base_path();
        $result = Terminal::execute('pnpm --version', $cwd);
        return $result['success'] ?? false;
    }

    /**
     * 检查Composer是否可用
     */
    public static function checkComposer(): bool
    {
        $cwd    = base_path();
        $result = Terminal::execute('composer --version', $cwd);
        return $result['success'] ?? false;
    }
}
