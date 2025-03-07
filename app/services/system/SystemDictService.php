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

namespace app\services\system;

use app\dao\system\SystemDictDao;
use madong\basic\BaseService;
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
class SystemDictService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemDictDao::class);
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
        $enumList = [];

        foreach ($directories as $directory) {
            $files = glob($directory . '/*.php');

            foreach ($files as $file) {
                $className = 'app\\num\\' . pathinfo($file, PATHINFO_FILENAME);
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
            'name'     => method_exists($className, 'getName') ? $className::getName() : $reflectionClass->getName(), // 枚举类的名称
            'dict_key' => $className,
            'items'    => [],
        ];
        foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
            $enumInstance        = $reflectionClass->getConstant($constantName);
            $enumInfo['items'][] = [
                'dict_item_key'   => $constantName, // 枚举成员的字典键名
                'dict_item_value' => method_exists($className, 'getDescription') ? $enumInstance->getDescription() : $constantValue, // 示例值
            ];
        }
        return $enumInfo; // 返回构建的枚举类信息
    }
}
