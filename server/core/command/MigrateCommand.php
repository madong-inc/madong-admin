<?php

namespace core\command;

use support\Db;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//创建迁移（自动表名）	migrate make RoleBelongsMenu	                生成小写蛇形文件名，提示表名
//创建迁移（手动表名）	migrate make RoleBelongsMenu --table=user_roles	使用指定表名，生成对应文件名
//创建种子	            migrate make-seed User	                        生成UserSeeder.php
//执行迁移	            migrate up	                                    输出已迁移的文件名
//迁移+种子	            migrate up --seed	                            先迁移，后运行种子
//回滚迁移	            migrate rollback	                            输出回滚的文件名，清理日志
//运行种子	            migrate seed	                                输出已运行的种子类名

/**
 * 数据迁移
 *
 * @author Mr.April
 * @since  1.0
 */
class MigrateCommand extends Command
{
    protected static string $defaultName = 'migrate';
    protected static string $defaultDescription = 'Database migration and seeding';
    protected ?string $connection = null;

    protected function configure(): void
    {
        $this->addArgument(
            'operate',
            InputArgument::OPTIONAL,
            'Operation: make/make-seed/up/rollback/seed',
            'up',
            ['make', 'make-seed', 'up', 'rollback', 'seed']
        );
        $this->addArgument('name', InputArgument::OPTIONAL, 'Migration/Seeder name (camelCase)');
        $this->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'DB connection [default: "default"]');
        $this->addOption('seed', null, InputOption::VALUE_NONE, 'Run seeders after migration');
        $this->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'Specify table name for migration (auto-generate if not provided)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $operate          = strtolower($input->getArgument('operate'));
        $name             = $input->getArgument('name');
        $this->connection = $input->getOption('connection');
        $runSeed          = $input->getOption('seed');

        $validOperations = ['make', 'make-seed', 'up', 'rollback', 'seed'];
        if (!in_array($operate, $validOperations)) {
            $output->writeln("<error>❌ Invalid operation: {$operate}</error>");
            return self::INVALID;
        }

        match ($operate) {
            'make' => $this->runMakeMigration($name, $input, $output),
            'make-seed' => $this->runMakeSeeder($name, $output),
            'up' => $this->runMigrations($output, $runSeed),
            'rollback' => $this->runRollback($output),
            'seed' => $this->runSeeders($output),
        };

        $output->writeln('<info>✅ Operation completed successfully!</info>');
        return self::SUCCESS;
    }

    // ------------------------------
    // 工具方法：字符串转换（保持不变）
    // ------------------------------
    /**
     * 驼峰转下划线（小写）
     */
    protected function camelToSnake(string $str): string
    {
        return strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $str));
    }

    /**
     * 下划线转驼峰（首字母大写）
     */
    protected function snakeToCamel(string $str): string
    {
        return str_replace('_', '', ucwords($str, '_'));
    }


    /**
     * 生成表名（驼峰转下划线 + 复数）
     */
    protected function generateTableName(string $name): string
    {
        return $this->camelToSnake($name);
    }

    /**
     * 创建迁移文件（修复多余下划线问题）
     */
    protected function runMakeMigration(string $name, InputInterface $input, OutputInterface $output)
    {
        $migrationDir = base_path('database/migrations');
        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0777, true);
        }

        // 1. 获取表名（手动指定或自动生成）
        $specifiedTableName = $input->getOption('table');
        $tableName          = $specifiedTableName ?? $this->generateTableName($name);

        // 提示表名来源
        if ($specifiedTableName) {
            $output->writeln("<info>ℹ️ Using specified table: {$tableName}</info>");
        } else {
            $output->writeln("<info>ℹ️ Generated table: {$tableName}</info>");
        }

        // 2. 生成「文件名前缀」（纯驼峰格式：create + 表名驼峰）
        $tableCamel     = $this->snakeToCamel($tableName); // 如 role_belongs_menus → RoleBelongsMenus
        $filenamePrefix = 'create' . $tableCamel; // 如 createRoleBelongsMenus（关键修正：去掉下划线）

        // 3. 转换为小写蛇形（最终文件名核心：create_role_belongs_menus）
        $snakeFilenamePrefix = $this->camelToSnake($filenamePrefix);

        // 4. 生成完整文件名（时间戳 + 小写蛇形前缀 + .php）
        $fileName = date('Y_m_d_His_') . $snakeFilenamePrefix . '.php';
        $filePath = "{$migrationDir}/{$fileName}";

        if (file_exists($filePath)) {
            $output->writeln("<error>❌ Migration file exists: {$fileName}</error>");
            return;
        }

        // 5. 生成迁移模板（匿名类，文件名小写蛇形）
        $template = $this->generateMigrationTemplate($tableName);
        file_put_contents($filePath, $template);
        $output->writeln("<info>📝 Migration created: {$fileName}</info>");
    }

    /**
     * 生成迁移模板（保持不变）
     */
    protected function generateMigrationTemplate(string $tableName): string
    {
        return <<<EOF
<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder \$schema): void
    {
        // 创建数据表（表名：{$tableName}）
        \$schema->create('{$tableName}', function(Blueprint \$table) {
            \$table->bigInteger('id')->primary()->comment('主键');
            \$table->timestamps();
        });
    }

    public function down(Builder \$schema): void
    {
        // 回滚：删除表（表名：{$tableName}）
        \$schema->dropIfExists('{$tableName}');
    }
};
EOF;
    }

    /**
     * 创建种子
     *
     * @param string                                            $name
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function runMakeSeeder(string $name, OutputInterface $output)
    {
        $seederClassPrefix = $this->snakeToCamel($name);

        $seederDir = base_path('database/seeds');
        if (!is_dir($seederDir)) {
            mkdir($seederDir, 0777, true);
        }

        // 🔑 文件名用驼峰前缀 + Seeder.php（确保与类名一致）
        $fileName = "{$seederClassPrefix}Seeder.php";
        $filePath = "{$seederDir}/{$fileName}";

        if (file_exists($filePath)) {
            $output->writeln("<error>❌ Seeder exists: {$fileName}</error>");
            return;
        }

        // 🔑 传入驼峰前缀生成模板（类名直接拼接前缀）
        $template = $this->generateSeederTemplate($seederClassPrefix);
        file_put_contents($filePath, $template);

        // ✅ 明确提示生成的文件和类名
        $output->writeln("<info>📝 Seeder created: {$fileName}</info>");
        $output->writeln("<comment>ℹ️ Seeder class: {$seederClassPrefix}Seeder</comment>");
    }

    /**
     * 生成种子模板
     *
     * @param string $name
     *
     * @return string
     */
    protected function generateSeederTemplate(string $name): string
    {
        $className = ucfirst($name) . 'Seeder';
        return <<<EOF
<?php

declare(strict_types=1);

use support\Db;

class {$className}
{
    public function run(): void
    {
        // 示例：插入用户数据
        /*
        Db::table('users')->insert([
            'username' => 'seed_user',
            'email' => 'seed@example.com',
            'password' => password_hash('secure', PASSWORD_DEFAULT),
        ]);
        */
    }
}
EOF;
    }


    protected function runSeeders(OutputInterface $output)
    {
        $seederDir = base_path('database/seeds');
        if (!is_dir($seederDir)) {
            $output->writeln("<comment>ℹ️ No seeders directory</comment>");
            return;
        }

        $files = glob("{$seederDir}/*.php");
        if (empty($files)) {
            $output->writeln("<comment>ℹ️ No seeders available</comment>");
            return;
        }

        foreach ($files as $file) {
            // 🔑 关键修改：拼接完整命名空间（database\seeds\文件名）
            $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
            $fullClassName      = 'database\\seeds\\' . $fileNameWithoutExt; // 完整类名：database\seeds\T1Seeder

            try {
                // 1. 加载种子文件（注册类到自动加载）
                require_once $file; // 用require_once避免重复加载

                // 2. 检查类是否已正确定义（用完整类名）
                if (!class_exists($fullClassName)) {
                    throw new \InvalidArgumentException("Seeder class '{$fullClassName}' not found in file '{$file}'");
                }

                // 3. 实例化种子类（用完整类名）
                $seederInstance = new $fullClassName();

                // 4. 执行run方法
                $seederInstance->run();

                $output->writeln("<info>✅ Seeded: {$fullClassName}</info>");
            } catch (\Throwable $e) {
                $output->writeln("<error>❌ Error seeding {$fullClassName}: {$e->getMessage()}</error>");
            }
        }
    }

    protected function runMigrations(OutputInterface $output, bool $runSeed = false)
    {
        $logFile            = $this->getMigrationLogFile();
        $rows               = $this->fetchMigrationRows($logFile);
        $latestBatch        = empty($rows) ? 0 : (int)end($rows)[0];
        $existingMigrations = array_column($rows, 1);
        $newMigrations      = [];

        $migrationDir = base_path('database/migrations');
        $dir          = new \DirectoryIterator($migrationDir);

        foreach ($dir as $file) {
            if ($file->isDot() || !$file->isFile()) continue;

            $basename = $file->getBasename('.php');
            if (!in_array($basename, $existingMigrations)) {
                $newMigrations[] = [$file->getRealPath(), $basename];
            }
        }

        if (!empty($newMigrations)) {
            sort($newMigrations);
            $schema = $this->getSchemaBuilder();

            foreach ($newMigrations as $migration) {
                $migrationClass = require $migration[0];
                $migrationClass->up($schema);
                $output->writeln("<info>⬆️ Migrated: {$migration[1]}</info>");
            }

            // 记录日志
            $batchNum = $latestBatch + 1;
            $logLines = array_map(
                fn($item) => "{$batchNum}," . $item[1],
                $newMigrations
            );
            file_put_contents($logFile, PHP_EOL . implode(PHP_EOL, $logLines), FILE_APPEND | LOCK_EX);
        }

        if ($runSeed) {
            $this->runSeeders($output);
        }
    }

    /**
     * 回滚
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function runRollback(OutputInterface $output)
    {
        $logFile       = $this->getMigrationLogFile();
        $rows          = $this->fetchMigrationRows($logFile);
        $latestBatch   = empty($rows) ? 1 : (int)end($rows)[0];
        $rollbackBatch = array_filter($rows, fn($row) => (int)$row[0] === $latestBatch);
        $rollbackBatch = array_reverse($rollbackBatch);

        if (!empty($rollbackBatch)) {
            $schema = $this->getSchemaBuilder();
            foreach ($rollbackBatch as $item) {
                $migrationClass = require base_path("database/migrations/{$item[1]}.php");
                $migrationClass->down($schema);
                $output->writeln("<info>⬇️ Rolled back: {$item[1]}</info>");
            }

            // 清理日志
            $remainingLogs = array_filter($rows, fn($row) => (int)$row[0] < $latestBatch);
            file_put_contents($logFile, implode(PHP_EOL, array_map(fn($row) => implode(',', $row), $remainingLogs)));
        }
    }

    // ------------------------------
    // 基础工具方法（保持不变）
    // ------------------------------
    protected function getMigrationLogFile(): string
    {
        return runtime_path('logs/' . ($this->connection ?? 'default') . '-migrations.log');
    }

    protected function fetchMigrationRows(string $logFile): array
    {
        if (!file_exists($logFile)) {
            touch($logFile);
        }
        $content = trim(file_get_contents($logFile));
        return empty($content) ? [] : array_map(fn($row) => explode(',', $row), explode(PHP_EOL, $content));
    }

    protected function getSchemaBuilder(): \Illuminate\Database\Schema\Builder
    {
        return Db::connection($this->connection)->getSchemaBuilder();
    }
}
