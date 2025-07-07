<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitcode.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace madong\exception;

use RuntimeException;

class Install
{
    public const WEBMAN_PLUGIN = true;

    /**
     * 文件路径映射关系
     *
     * @var array<string, string>
     */
    protected static array $pathRelation = [
        'config/plugin/madong/exception' => 'config/plugin/madong/exception',
    ];

    /**
     * 安装插件
     *
     * @throws RuntimeException 当安装过程中出现错误时抛出
     */
    public static function install(): void
    {
        static::installByRelation();
    }

    /**
     * 卸载插件
     *
     * @throws RuntimeException 当卸载过程中出现错误时抛出
     */
    public static function uninstall(): void
    {
        self::uninstallByRelation();
    }

    /**
     * 根据路径关系安装文件
     *
     * @throws RuntimeException 当文件操作失败时抛出
     */
    protected static function installByRelation(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            $sourcePath = __DIR__ . "/{$source}";
            $destPath   = base_path() . "/{$dest}";

            if (!file_exists($sourcePath)) {
                throw new RuntimeException("Source path does not exist: {$sourcePath}");
            }

            self::ensureDirectoryExists(dirname($destPath));
            self::copyFiles($sourcePath, $destPath);
        }
    }

    /**
     * 根据路径关系卸载文件
     *
     * @throws RuntimeException 当文件操作失败时抛出
     */
    protected static function uninstallByRelation(): void
    {
        foreach (static::$pathRelation as $dest) {
            $path = base_path() . "/{$dest}";

            if (!file_exists($path)) {
                continue;
            }

            self::removePath($path);
        }
    }

    /**
     * 确保目录存在
     *
     * @param string $dir 目录路径
     *
     * @throws RuntimeException 当创建目录失败时抛出
     */
    protected static function ensureDirectoryExists(string $dir): void
    {
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException("Failed to create directory: {$dir}");
        }
    }

    /**
     * 复制文件或目录
     *
     * @param string $source 源路径
     * @param string $dest   目标路径
     *
     * @throws RuntimeException 当复制失败时抛出
     */
    protected static function copyFiles(string $source, string $dest): void
    {
        if (!copy_dir($source, $dest)) {
            throw new RuntimeException("Failed to copy from {$source} to {$dest}");
        }
    }

    /**
     * 删除文件或目录
     *
     * @param string $path 要删除的路径
     *
     * @throws RuntimeException 当删除失败时抛出
     */
    protected static function removePath(string $path): void
    {
        if (is_link($path)) {
            if (!unlink($path)) {
                throw new RuntimeException("Failed to remove symlink: {$path}");
            }
        } elseif (!remove_dir($path)) {
            throw new RuntimeException("Failed to remove path: {$path}");
        }
    }
}
