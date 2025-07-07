<?php

namespace madong\captcha;

use RuntimeException;

class Install
{
    public const WEBMAN_PLUGIN = true;

    /**
     * Path relations for installation
     * @var array
     */
    protected static array $pathRelation = [
        'config/plugin/madong/captcha' => 'config/plugin/madong/captcha',
    ];

    /**
     * Install the plugin
     * @return void
     * @throws RuntimeException If installation fails
     */
    public static function install(): void
    {
        static::installByRelation();
    }

    /**
     * Uninstall the plugin
     * @return void
     * @throws RuntimeException If uninstallation fails
     */
    public static function uninstall(): void
    {
        self::uninstallByRelation();
    }

    /**
     * Install files based on path relations
     * @return void
     * @throws RuntimeException If file operations fail
     */
    protected static function installByRelation(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            $destinationPath = base_path() . '/' . $dest;
            $sourcePath = __DIR__ . '/' . $source;

            // Create parent directory if needed
            $parentDir = dirname($destinationPath);
            if (!is_dir($parentDir) && !mkdir($parentDir, 0777, true) && !is_dir($parentDir)) {
                throw new RuntimeException(sprintf('Directory "%s" could not be created', $parentDir));
            }

            // Copy files
            if (!copy_dir($sourcePath, $destinationPath)) {
                throw new RuntimeException(sprintf('Failed to copy from "%s" to "%s"', $sourcePath, $destinationPath));
            }
        }
    }

    /**
     * Uninstall files based on path relations
     * @return void
     * @throws RuntimeException If file operations fail
     */
    protected static function uninstallByRelation(): void
    {
        foreach (static::$pathRelation as $dest) {
            $path = base_path() . '/' . $dest;

            if (!file_exists($path)) {
                continue;
            }

            if (!remove_dir($path)) {
                throw new RuntimeException(sprintf('Failed to remove path "%s"', $path));
            }
        }
    }
}
