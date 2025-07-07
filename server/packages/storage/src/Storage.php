<?php

namespace madong\storage;

use BadMethodCallException;
use madong\storage\adapter\AbstractFileAdapter;
use madong\storage\exception\StorageException;

/**
 * @see Storage
 * @mixin Storage
 * @method static array uploadFile(array $config = [])                          上传文件
 * @method static array uploadBase64(string $base64, string $extension = 'png') 上传Base64文件
 * @method static array uploadServerFile(string $file_path)                     上传服务端文件
 */
class Storage
{
    public const MODE_LOCAL = 'local';
    public const MODE_OSS = 'oss';
    public const MODE_COS = 'cos';
    public const MODE_QINIU = 'qiniu';

    protected static ?AbstractFileAdapter $adapter = null;
    protected static array $configCache = [];

    /**
     * 静态代理调用适配器方法
     *
     * @throws StorageException
     */
    public static function __callStatic(string $method, array $arguments)
    {
        if (static::$adapter === null) {
            throw new StorageException('Storage adapter not initialized. Call Storage::config() first.');
        }

        if (!method_exists(static::$adapter, $method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class(static::$adapter),
                $method
            ));
        }

        return static::$adapter->{$method}(...$arguments);
    }

    /**
     * 初始化存储配置
     *
     * @param string|null $storageMode  存储模式 (self::MODE_*)
     * @param bool        $isFileUpload 是否处理上传文件
     * @param array       $customConfig 自定义配置（覆盖默认）
     *
     * @throws StorageException
     */
    public static function config(
        ?string $storageMode = null,
        bool    $isFileUpload = true,
        array   $customConfig = []
    ): void
    {
        $config = self::loadConfig($storageMode);

        if (!isset($config['adapter']) || !class_exists($config['adapter'])) {
            throw new StorageException(sprintf(
                'Storage adapter class not found: %s',
                $config['adapter'] ?? 'null'
            ));
        }

        static::$adapter = new $config['adapter'](array_merge(
            $config,
            $customConfig,
            ['is_file_upload' => $isFileUpload]
        ));

        if (!static::$adapter instanceof AbstractFileAdapter) {
            throw new StorageException(sprintf(
                'Adapter must extend %s, %s given',
                AbstractFileAdapter::class,
                get_class(static::$adapter)
            ));
        }
    }

    /**
     * 获取当前适配器实例
     *
     * @throws StorageException
     */
    public static function getAdapter(): AbstractFileAdapter
    {
        if (static::$adapter === null) {
            throw new StorageException('Storage adapter not initialized');
        }

        return static::$adapter;
    }

    /**
     * 加载配置文件
     */
    protected static function loadConfig(?string $storageMode): array
    {
        if (empty(self::$configCache)) {
            self::$configCache = config('plugin.madong.storage.app.storage', []);
        }

        $mode = $storageMode ?? self::$configCache['default'] ?? self::MODE_LOCAL;

        return self::$configCache[$mode] ?? throw new StorageException(
            "Storage config for mode '$mode' not found"
        );
    }
}
