<?php

namespace madong\services\upload;

use app\services\system\SystemConfigService;
use madong\exception\UploadException;
use support\Container;

/**
 *
 * 文件上传
 * @author Mr.April
 * @since  1.0
 */
class UploadFile
{

    const CONFIG_GROUP_CODE = 'system_storage';

    static array $allowStorage = [];

    protected static function init(): void
    {
        $configAllowStorage = config('upload.adapter_classes');
        self::$allowStorage = array_unique(array_merge([
            'local',
            'oss',
            'cos',
            'qiniu',
            's3',
        ], array_keys($configAllowStorage)));
    }

    /**
     * 获取配置信息
     *
     * @param string $name
     *
     * @return array|null
     */
    public static function getConfig(string $name = ''): ?array
    {
        $systemConfigService = Container::make(SystemConfigService::class);
        $config              = $systemConfigService->getConfig($name, self::CONFIG_GROUP_CODE);
        return $config ? json_decode($config, true) : [];
    }

    /**
     * @desc 获取默认配置
     * @return array
     */
    public static function getDefaultConfig(): array
    {
        $systemConfigService = Container::make(SystemConfigService::class);
        $basicConfig         = $systemConfigService->getConfig('basic', self::CONFIG_GROUP_CODE);
        if (empty($basicConfig)) {
            return [
                'default'      => 'local',
                'single_limit' => 1024,
                'total_limit'  => 1024,
                'nums'         => 1,
                'include'      => ['png'],
                'exclude'      => ['mp4'],
            ];
        }
        return json_decode($basicConfig, true);
    }

    public static function disk(string|null $storage = null, bool $is_file_upload = true): UploadFileInterface
    {
        self::init();
        $defaultConfig = self::getDefaultConfig();
        if (empty($storage)) {
            $adapter       = $defaultConfig['default'];
            $adapterConfig = self::getConfig($adapter);
        } else {
            $adapter       = $storage;
            $adapterConfig = self::getConfig($storage);
        }
        if (!in_array($adapter, self::$allowStorage)) {
            throw new UploadException("不支持的存储类型:" . $adapter);
        }
        $config = array_merge($defaultConfig, $adapterConfig, ['_is_file_upload' => $is_file_upload]);
        $handle = config('upload.adapter_classes.' . $adapter);
        if (!$handle) {
            throw new UploadException("未找到适配器处理器:" . $handle);
        }
        return new $handle($config);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return static::disk()->{$name}(...$arguments);
    }
}
