<?php

namespace core\plugin\traits;

/**
 * 种子操作 Trait
 */
trait SeedTrait
{
    /**
     * 插件临时目录路径
     */
    const RUNTIME_PLUGIN_PATH = 'runtime/install/plugin';

    /**
     * 运行种子
     */
    public function runSeeds(): void
    {
        $seedsDir = $this->findSeedsDir();
        
        if (!$seedsDir) {
            $this->output("⚠️ No seeds directory found");
            return;
        }

        $this->output("📁 Seeds dir: {$seedsDir}");

        $executed = $this->getExecutedSeeds();

        $files = glob($seedsDir . '/*.php');

        $pending = [];
        foreach ($files as $file) {
            $basename = basename($file, '.php');
            $seedName = str_replace('Seeder', '', $basename);
            if (!in_array($seedName, $executed)) {
                $pending[] = [$file, $basename];
            }
        }

        sort($pending);

        if (empty($pending)) {
            $this->output("✅ No pending seeds");
            return;
        }

        $this->output("📋 Pending seeds: " . count($pending));

        foreach ($pending as $seed) {
            $this->runSeed($seed[0], $seed[1]);
        }

        $this->output("✅ Seeds completed");
    }
    
    /**
     * 运行单个种子
     */
    protected function runSeed(string $file, string $className): void
    {
        $this->output("  📝 Seeding: {$className}");

        require_once $file;

        $seeder = null;

        $classes = get_declared_classes();
        foreach ($classes as $cls) {
            if (str_ends_with($cls, '\\' . $className) || $cls === $className) {
                $seeder = new $cls();
                break;
            }
        }

        if (!$seeder) {
            $this->output("  ❌ Seeder class not found: {$className}");
            return;
        }

        try {
            if (method_exists($seeder, 'run')) {
                $seeder->run();
            }
            $this->output("  ✅ Seeded: {$className}");

            $this->recordSeed(str_replace('Seeder', '', $className));
        } catch (\Throwable $e) {
            $this->output("  ❌ Error: {$e->getMessage()}");
        }
    }

    /**
     * 查找种子目录
     */
    protected function findSeedsDir(): ?string
    {
        $resourceDir = $this->getConfig('resource.seed', 'database/seeds');
        
        $dirs = [
            $this->pluginPath . '/resource/database/seeds',
            $this->pluginPath . '/' . $resourceDir,
            $this->pluginPath . '/install/seeds',
            base_path("resource/database/seeds/plugin/{$this->pluginName}"),
        ];
        
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                return $dir;
            }
        }
        
        return null;
    }

    /**
     * 获取种子日志文件
     */
    protected function getSeedLogFile(): string
    {
        return runtime_path(self::RUNTIME_PLUGIN_PATH . '/' . $this->pluginName . '-seeds.log');
    }

    /**
     * 获取已执行的种子
     */
    protected function getExecutedSeeds(): array
    {
        $logFile = $this->getSeedLogFile();
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $content = trim(file_get_contents($logFile));
        
        if (empty($content)) {
            return [];
        }
        
        return array_filter(explode(PHP_EOL, $content));
    }

    /**
     * 记录种子
     */
    protected function recordSeed(string $name): void
    {
        $logFile = $this->getSeedLogFile();
        
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, PHP_EOL . $name, FILE_APPEND | LOCK_EX);
    }
}
