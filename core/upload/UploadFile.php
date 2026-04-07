<?php

namespace core\upload;

use app\service\admin\system\ConfigService;
use core\exception\handler\UploadException;
use support\Container;

/**
 * 文件上传
 *
 * @author Mr.April
 * @since  1.0
 * @method static uploadFile()
 */
class UploadFile
{

    static array $allowStorage = [];

    protected static function init(): void
    {
        $configAllowStorage = config('core.upload.app.adapter_classes');
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
     * @param array  $default
     *
     * @return array|null
     * @throws \Exception
     */
    public static function config(string $name = '', array $default = []): ?array
    {
        /** @var  ConfigService $systemConfigService */
        $systemConfigService = Container::make(ConfigService::class);
        $config              = $systemConfigService->config($name, $default);
        return $config ?? [];
    }

    /**
     * @throws \core\exception\handler\UploadException
     */
    public static function disk(string|null $storage = null, bool $is_file_upload = true): UploadFileInterface
    {
        self::init();
        $configService = Container::make(ConfigService::class);
        $defaultConfig = $configService->config('upload', [
            'mode'         => 'local',
            'single_limit' => 1024 * 1024,
            'total_limit'  => 1024 * 1024,
            'nums'         => 10,
            'exclude'      => ['php', 'ext', 'exe'],
        ]);
        if (empty($storage)) {
            $adapter       = $defaultConfig['mode'];
            $adapterConfig = $configService->config($adapter);
        } else {
            $adapter       = $storage;
            $adapterConfig = $configService->config($storage);
        }
        if (!in_array($adapter, self::$allowStorage)) {
            throw new UploadException("不支持的存储类型:" . $adapter);
        }
        $config = array_merge($defaultConfig, $adapterConfig, ['_is_file_upload' => $is_file_upload]);
        $handle = config('core.upload.app.adapter_classes.' . $adapter);
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
