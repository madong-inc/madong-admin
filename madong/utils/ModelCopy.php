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

namespace madong\utils;

use ReflectionClass;
use ReflectionProperty;
use stdClass;

/**
 *
 * @author Mr.April
 * @since  1.0
 */
class ModelCopy
{
    /**
     * 将传入的属性复制到模型
     *
     * @param object $param 源对象
     * @param object $src   目标模型对象
     */
    public static function copyProperties(object $param, object $src): void
    {
        $paramProps = get_object_vars($param);
        $fields     = $src->getFields() ?? [];
        foreach ($paramProps as $key => $value) {
            if (in_array($key, $fields)) {
                $src->{$key} = $value;
            }
        }
    }

    /**
     * 给对象类赋值私有属性
     *
     * @param object $data
     * @param string $className
     * @param bool   $strictMode 是否严格模式
     *
     * @return object
     * @throws \ReflectionException
     */
    public static function jsonObjToLfModel(object $data, string $className, bool $strictMode = false): object
    {
        $reflection = new ReflectionClass($className);
        $instance   = $reflection->newInstanceWithoutConstructor(); // 假设无参构造函数或需要绕过构造函数

        while ($reflection) {
            // 对public private protected 类型属性赋值
            $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC);

            foreach ($properties as $property) {
                $property->setAccessible(true);
                $name  = $property->getName();
                $value = $data->{$name} ?? '';

                // 统一处理属性名，支持下划线和驼峰命名
                $camelCaseName  = self::convertToCamelCase($name);
                $underscoreName = self::convertToUnderscore($name);

                // 检查属性类型和数据中的值，避免将数组类型的属性设置为 null
                if (is_array($property->getType()) && !isset($data->{$name})) {
                    continue; // 如果属性类型为数组且数据中没有对应的值，跳过此次循环
                }

                // 根据数据中的属性名赋值
                if (isset($data->{$name})) {
                    $property->setValue($instance, $value);
                } elseif (isset($data->{$camelCaseName}) && !$strictMode) {
                    $property->setValue($instance, $data->{$camelCaseName});
                } elseif (isset($data->{$underscoreName}) && !$strictMode) {
                    $property->setValue($instance, $data->{$underscoreName});
                }
            }
            $reflection = $reflection->getParentClass();
        }
        return $instance;
    }

    /**
     * @param array $data
     *
     * @return array[]
     */
    public static function getSearchData(array $data): array
    {
        $search = [];
        $where  = [];
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $search[]    = $key;
                $where[$key] = $value;
            }
        }
        return [$search, $where];
    }

    /**
     * 下划线转驼峰(首字母大写)
     *
     * @param $string
     *
     * @return string
     */
    public static function underscoreToCamelCase($string): string
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return ucfirst($str);
    }

    private static function convertToCamelCase($propertyName): string
    {
        $propertyName = str_replace('_', ' ', $propertyName);
        $propertyName = ucwords($propertyName);
        $propertyName = str_replace(' ', '', $propertyName);
        return lcfirst($propertyName);
    }

    private static function convertToUnderscore($propertyName): string
    {
        $underscoreName = preg_replace('/(?<!^)[A-Z]/', '_$0', $propertyName);
        return strtolower($underscoreName);
    }

}
