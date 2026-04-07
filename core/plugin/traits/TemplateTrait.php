<?php

namespace core\plugin\traits;

/**
 * 模板资源操作 Trait
 */
trait TemplateTrait
{
    /**
     * 复制模板资源到目标目录
     */
    protected function copyTemplates(): void
    {
        $templateDir = $this->pluginPath . '/resource/template';

        if (!is_dir($templateDir)) {
            $this->output("📂 No template directory found, skipping...");
            return;
        }

        $this->output("📂 Copying templates from {$templateDir}...");

        $this->copyTemplateDir('admin');
        $this->copyTemplateDir('web');
    }

    /**
     * 复制单个端的模板
     */
    protected function copyTemplateDir(string $endpoint): void
    {
        $resourceDir = $this->getConfig('resource.template', 'template');
        $targetBase = $this->getConfig("template.{$endpoint}", "resource/view/{$endpoint}/plugin");

        $sourceDir = $this->pluginPath . '/resource/' . $resourceDir . '/' . $endpoint;
        $targetDir = $this->getProjectRoot() . '/' . $targetBase . '/' . $this->pluginName;

        if (!is_dir($sourceDir)) {
            $this->output("  ⚠️ {$endpoint}: Template source not found");
            return;
        }

        if (!is_dir(dirname($targetDir))) {
            mkdir(dirname($targetDir), 0755, true);
        }

        $this->recurseCopy($sourceDir, $targetDir);

        $this->output("  ✅ {$endpoint}: Templates copied to {$targetDir}");
    }

    /**
     * 递归复制目录
     */
    protected function recurseCopy(string $source, string $target): void
    {
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $file;
            $targetPath = $target . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->recurseCopy($sourcePath, $targetPath);
            } else {
                copy($sourcePath, $targetPath);
            }
        }
    }

    /**
     * 删除插件模板资源
     */
    protected function deleteTemplates(): void
    {
        $this->output("🗑️ Deleting plugin templates...");

        $this->deleteTemplateDir('admin');
        $this->deleteTemplateDir('web');
    }

    /**
     * 删除单个端模板
     */
    protected function deleteTemplateDir(string $endpoint): void
    {
        $targetBase = $this->getConfig("template.{$endpoint}", "resource/view/{$endpoint}/plugin");

        $targetDir = $this->getProjectRoot() . '/' . $targetBase . '/' . $this->pluginName;

        if (!is_dir($targetDir)) {
            $this->output("  ⚠️ {$endpoint}: Template directory not found");
            return;
        }

        $this->recurseDelete($targetDir);

        $this->output("  ✅ {$endpoint}: Templates deleted");
    }

    /**
     * 递归删除目录
     */
    protected function recurseDelete(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->recurseDelete($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    /**
     * 导入模板（子类可重写）
     */
    protected function importTemplates(): void
    {
        $this->copyTemplates();
    }

    /**
     * 获取项目根目录
     */
    protected function getProjectRoot(): string
    {
        return dirname(base_path());
    }
}
