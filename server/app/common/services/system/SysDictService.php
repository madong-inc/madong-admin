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

namespace app\common\services\system;

use app\common\dao\system\SysDictDao;
use core\abstract\BaseService;
use ReflectionClass;
use ReflectionException;
use support\Container;

/**
 * 数据字段服务
 *
 * @author Mr.April
 * @since  1.0
 * @method update($where, $data)
 */
class SysDictService extends BaseService
{

    public array $enumDirectory = [];

    public function __construct()
    {
        $this->enumDirectory = config('app.enum_scan_directories');//获取配置扫描目录
        $this->dao           = Container::make(SysDictDao::class);
    }

    /**
     * 扫描枚举目录
     *
     * @param array $directories
     *
     * @return array
     */
    public function scanEnums(array $directories): array
    {
        //待优化扫描后放到redis
        $enumList = [];
        foreach ($directories as $directory) {
            $files = glob($directory . '/*.php');
            foreach ($files as $file) {
                $className = $this->getClassNameFromFile($file);
                if ($this->isEnumClass($className)) {
                    $enumInfo = $this->getEnumInfo($className);

                    if ($enumInfo) {
                        $enumList[] = $enumInfo;
                    }
                }
            }
        }
        return $enumList;
    }

    /**
     * 通过命名空间获取枚举数据
     *
     * @param string $code
     * @param array  $directories
     *
     * @return array
     */
    public function getEnumByNamespace(string $code, array $directories = []): array
    {
        $item = [];
        if (empty($directories)) {
            $directories = $this->enumDirectory;
        }
        $code = $this->convertDotToNamespace($code);//兼容.命名空间写法

        $enumList = $this->scanEnums($directories);
        foreach ($enumList as $key => $enum) {
            if ($enum['dict_key'] == $code) {
                $items = $enum['items'] ?? [];
                foreach ($items as $i) {
                    $item[] = [
                        'label' => $i['dict_item_label'],
                        'value' => $i['dict_item_value'],
                        'color' => !empty($i['color']) ? $i['color'] : $this->generateEnumColor($i['dict_item_value']),//如果枚举类没有定义字典颜色使用规则生成对应的颜色值
                        'ext'   => [],
                    ];
                }
                break;
            }
        }
        return $item ?? [];
    }

    /**
     * 是否枚举类
     *
     * @param $className
     *
     * @return bool
     */
    private function isEnumClass($className): bool
    {
        try {
            $reflectionClass = new ReflectionClass($className);
            return $reflectionClass->isEnum();
        } catch (ReflectionException $e) {
            return false; // 如果遇到错误则返回 false
        }
    }

    /**
     * 枚举数据
     *
     * @param $className
     *
     * @return array
     */
    private function getEnumInfo($className): array
    {
        $reflectionClass = new ReflectionClass($className);
        $enumInfo        = [
            'name'     => method_exists($className, 'getEnumClassName') ? $className::getEnumClassName() : basename(str_replace('\\', '/', $className)),
            'dict_key' => $className,
            'items'    => [],
        ];

        foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
            // 安全获取枚举实例
            $enumValue           = $constantValue;
            $enumInfo['items'][] = [
                'dict_item_key'   => $constantName,
                'dict_item_value' => $enumValue->value ?? '',
                'dict_item_label' => method_exists($enumValue, 'label') ? $enumValue->label() : '', // 兼容label方法检查
                'color'           => method_exists($enumValue, 'color') ? $enumValue->color() : '', // 关键优化点
                'ext'             => [],
            ];
        }
        return $enumInfo; // 返回构建的枚举类信息
    }

    private function getClassNameFromFile(string $file): string
    {
        // 获取文件内容并解析命名空间
        $namespace   = '';
        $fileContent = file_get_contents($file);
        if (preg_match('/namespace\s+([^;]+);/', $fileContent, $matches)) {
            $namespace = trim($matches[1]);
        }
        // 获取类名
        $className = pathinfo($file, PATHINFO_FILENAME);
        // 返回完整的类名
        return $namespace ? $namespace . '\\' . $className : $className;
    }

    /**
     * 生成枚举项颜色（支持自定义规则 + 默认规则）
     *
     * @param string|int $value 枚举值
     *
     * @return string 颜色标识（CSS类名或HSL字符串）
     */
    private function generateEnumColor(string|int $value): string
    {
        // 1. 自定义颜色配置（优先级最高）
        $customColors = [
            1  => 'success',   // 成功 → 绿色系
            0  => 'error',     // 失败 → 红色系
            10 => 'processing',// 进行中 → 蓝色系
            20 => 'success',   // 已完成 → 绿色系
            99 => 'disabled',  // 已废弃 → 灰色系
            // 可扩展更多自定义规则...
        ];
        if (isset($customColors[$value])) {
            return $customColors[$value];
        }
        // 2. 通用规则生成（未自定义时回退）
        return $this->generateDefaultColor($value);
    }

    /**
     * 默认颜色生成规则（HSL空间）
     *
     * @param int|string $value
     *
     * @return string
     */
    private function generateDefaultColor(int|string $value): string
    {
        $strValue = (string)$value;
        $hash     = md5($strValue);
        $hue      = (hexdec(substr($hash, 0, 2)) / 255) * 360;
        return sprintf('hsl(%d, 70%%, 50%%)', (int)$hue);
    }

    /**
     * 路径.转斜杠
     *
     * @param string $dotPath
     * @param bool   $validate
     * @param string $rootNamespace
     *
     * @return string
     */
    private function convertDotToNamespace(string $dotPath, bool $validate = true, string $rootNamespace = ''): string
    {
        // 多层过滤处理
        $normalized = preg_replace(['/\.+/', '/\\\\+/'], ['.', '\\'], $dotPath);
        $className  = strtr($normalized, ['.' => '\\']);
        // 添加根命名空间
        if ($rootNamespace) {
            $className = rtrim($rootNamespace, '\\') . '\\' . ltrim($className, '\\');
        }
        // 智能验证
//        if ($validate && !class_exists($className)) {
//            throw new \InvalidArgumentException("类 {$className} 未找到，请检查路径配置");
//        }
        return $className;
    }
}
