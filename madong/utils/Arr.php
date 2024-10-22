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

use ArrayAccess;
use InvalidArgumentException;
use stdClass;

class Arr
{

    /**
     * 用于判断一个值是否可以被视为可访问的数组或实现了 ArrayAccess 接口的对象
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * 数组中添加不存在的元素
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function add(array $array, string $key, mixed $value): array
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }
        return $array;
    }

    /**
     * 将数组折叠单个数组
     *
     * @param array $array
     *
     * @return array
     */
    public static function collapse(array $array): array
    {
        $results = [];
        foreach ($array as $values) {
            if (!is_array($values)) {
                continue;
            }
            $results = array_merge($results, array_values($values));
        }
        return $results;
    }

    /**
     * 交叉给定数组返回所有排序数组
     *
     * @param array ...$arrays
     *
     * @return array|array[]
     */
    public static function crossJoin(array ...$arrays): array
    {
        $results = [[]];
        foreach ($arrays as $array) {
            $append = [];
            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[] = $item;
                    $append[]  = $product;
                    array_pop($product);
                }
            }
            $results = $append;
        }
        return $results;
    }

    /**
     * 分别获取数组的键名和键值，然后将它们作为一个包含两个元素的数组返回。
     *
     * @param array $array
     *
     * @return array
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * 扁平化一个多维数组
     *
     * @param array       $array
     * @param string|null $prepend
     *
     * @return array
     */
    public static function dot(array $array, string|null $prepend = ''): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    /**
     * 获取除指定键数组外的所有给定数组。
     *
     * @param array        $array
     * @param string|array $keys
     *
     * @return array
     */
    public static function except(array $array, string|array $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * 移除指定的键名，支持多级键名的处理
     *
     * @param array $array
     * @param array $keys
     */
    public static function forget(array &$array, array $keys): void
    {
        $original = &$array;
        $keys     = (array)$keys;
        if (count($keys) === 0) {
            return;
        }
        foreach ($keys as $key) {
            if (static::exists($array, $key)) {
                unset($array[$key]);
                continue;
            }
            $parts = explode('.', $key);
            $array = &$original;
            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }
            unset($array[array_shift($parts)]);
        }
    }

    /**
     * 确定给定的键名是否存在于提供的数组中
     *
     * @param array|\ArrayAccess $array
     * @param string|int         $key
     *
     * @return bool
     */
    public static function exists(array|ArrayAccess $array, string|int $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * 数组中第一个满足指定条件的元素。如果没有传入回调函数，则直接返回数组的第一个元素；如果传入了回调函数，则根据回调函数的条件来确定返回的元素。如果没有满足条件的元素，则返回指定的默认值
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     *
     * @return mixed|null
     */
    public static function first(array $array, callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return $default;
            }

            foreach ($array as $item) {
                return $item;
            }
        }
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }
        return $default;
    }

    /**
     * 返回数组中最后一个满足指定条件的元素。如果没有传入回调函数，则直接返回数组的最后一个元素；如果传入了回调函数，则先将数组反转
     *
     * @param array         $array
     * @param callable|null $callback
     * @param               $default
     *
     * @return false|mixed|null
     */
    public static function last(array $array, callable $callback = null, $default = null): mixed
    {
        if (is_null($callback)) {
            return empty($array) ? $default : end($array);
        }

        $reversedArray = array_reverse($array, true);
        return static::first($reversedArray, $callback, $default);
    }

    /**
     * 根据指定的键名从数组中获取对应的值，支持使用 "dot" 符号来访问多维数组中的元素
     *
     * @param \ArrayAccess|array $array
     * @param string|null        $key
     * @param mixed              $default
     *
     * @return mixed
     */
    public static function get(\ArrayAccess|array $array, string|null $key, mixed $default = null): mixed
    {
        if (!static::accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }
        return $array;
    }

    /**
     * 检测一个数组或单个数组
     *
     * @param \ArrayAccess|array $array
     * @param array|string       $keys
     *
     * @return bool
     */
    public static function has(\ArrayAccess|array $array, array|string $keys): bool
    {
        $keys = (array)$keys;

        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 检查数组是否关联
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * 数组中过滤出指定的键名对应的元素
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function only(array $array, array|string $keys): array
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * 输入的数组中提取指定键名对应的值，并根据需要将提取的值组装成一个新的数组返回
     *
     * @param array       $array
     * @param string      $value
     * @param string|null $key
     *
     * @return array
     */
    public static function pluck(array $array, string $value, ?string $key = null): array
    {
        $results = [];
        [$value, $key] = static::explodePluckParameters($value, $key);
        foreach ($array as $item) {
            $itemValue = data_get($item, $value);
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);
                if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                    $itemKey = (string)$itemKey;
                }
                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * 将value key 进行分割返回数组value&key 集合
     *
     * @param string|array          $value
     * @param string|int|array|null $key
     *
     * @return array
     */
    protected static function explodePluckParameters(string|array $value, string|int|array|null $key): array
    {
        $value = is_string($value) ? explode('.', $value) : $value;
        $key   = is_null($key) || is_array($key) ? $key : explode('.', $key);
        return [$value, $key];
    }

    /**
     * 将值添加到数组开头
     *
     * @param array       $array
     * @param mixed       $value
     * @param string|null $key
     *
     * @return array
     */
    public static function prepend(array $array, mixed $value, ?string $key = null): array
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }
        return $array;
    }

    /**
     * 从数组中取出一个值并删除它
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function pull(array &$array, string $key, mixed $default = null): mixed
    {
        $value = static::get($array, $key, $default);
        static::forget($array, $key);
        return $value;
    }

    /**
     * 从数组中获取一个或多个随机值
     *
     * @param array    $array
     * @param int|null $number
     *
     * @return mixed
     */
    public static function random(array $array, ?int $number = 1): mixed
    {
        $requested = is_null($number) ? 1 : $number;
        $count     = count($array);
        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }
        if (is_null($number)) {
            return $array[array_rand($array)];
        }
        if ($number === 0) {
            return [];
        }
        $keys    = array_rand($array, $number);
        $results = [];
        foreach ((array)$keys as $key) {
            $results[] = $array[$key];
        }
        return $results;
    }

    /**
     * 使用“点”表示法在数组中设置值
     *
     * @param array           $array
     * @param string|int|null $key
     * @param mixed           $value
     *
     * @return array
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $currentKey = array_shift($keys);
            if (!isset($array[$currentKey]) || !is_array($array[$currentKey])) {
                $array[$currentKey] = [];
            }
            $array = &$array[$currentKey];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }

    /**
     * 使用可选的种子值随机洗牌数组
     *
     * @param array    $array
     * @param int|null $seed
     *
     * @return array
     */
    public static function shuffle(array $array, ?int $seed = null): array
    {
        if (is_null($seed)) {
            shuffle($array);
        } else {
            srand($seed);
            usort($array, function () {
                return rand(-1, 1);
            });
        }
        return $array;
    }

    /**
     * 递归排序数组
     *
     * @param array $array
     *
     * @return array
     */
    public static function sortRecursive(array $array): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }
        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }
        return $array;
    }

    /**
     * 将数组转换为查询字符串
     *
     * @param array $array
     *
     * @return string
     */
    public static function query(array $array): string
    {
        return http_build_query($array, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * 使用给定的回调筛选数组
     *
     * @param array    $array
     * @param callable $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 如果该值不是数组，则将其包装在数组中
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function wrap(mixed $value): array
    {
        if (is_null($value)) {
            return [];
        }
        return is_array($value) ? $value : [$value];
    }

    /**
     * 多维数组转对象
     *
     * @param object|array $array
     *
     * @return \stdClass
     */
    public static function arrayToObject(object|array $array): stdClass
    {
        $object = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (isset($value[0])) { // 处理下标数组
                    $object->$key = self::arrayToObject($value); // 递归处理子数组
                } else {
                    $object->$key = self::arrayToObject($value); // 处理关联数组
                }
            } else {
                if (is_numeric($value)) {
                    $object->$key = $value + 0; // 将字符串转换为数字类型
                } else {
                    $object->$key = $value;
                }

            }
        }
        return $object;
    }

    /**
     * 数组排序
     *
     * @param array  $items
     * @param string $key
     * @param bool   $reverse 排序方式
     *
     * @return array
     */
    public static function sortItems(array $items, string $key, bool $reverse = false): array
    {
        usort($items, function ($a, $b) use ($key, $reverse) {
            $valueA = self::getValueByKey($a, $key);
            $valueB = self::getValueByKey($b, $key);

            if ($valueA === $valueB) {
                return 0;
            }

            return ($valueA < $valueB) ? ($reverse ? 1 : -1) : ($reverse ? -1 : 1);
        });

        return $items;
    }

    private static function getValueByKey($item, string $key)
    {
        if (is_array($item) && array_key_exists($key, $item)) {
            return $item[$key];
        } elseif (is_object($item) && property_exists($item, $key)) {
            return $item->$key;
        }
        return null;
    }

    /**
     * 获取数组中指定的列
     *
     * @param array      $source
     * @param string|int $column
     *
     * @return array
     */
    public static function getArrayColumn(array $source, string|int $column): array
    {
        $columnArr = [];
        foreach ($source as $item) {
            $columnArr[] = $item[$column];
        }
        return $columnArr;
    }

    /**
     * 批量获取数组中指定的列
     *
     * @param array $source
     * @param array $columns
     *
     * @return array
     */
    public static function getArrayColumns(array $source, array $columns): array
    {
        $columnArr = [];
        foreach ($source as $item) {
            $tempArr = [];
            foreach ($columns as $key) {
                $temp = explode('.', $key);
                if (count($temp) > 1) {
                    $tempArr[$key] = $item[$temp[0]][$temp[1]];
                } else {
                    $tempArr[$key] = $item[$key];
                }
            }
            $columnArr[] = $tempArr;
        }
        return $columnArr;
    }

    /**
     * 把二维数组中某列设置为key返回
     *
     * @param array  $array  输入数组
     * @param string $field  要作为键的字段名
     * @param bool   $unique 要做键的字段是否唯一(该字段与记录是否一一对应)
     *
     * @return array
     */
    public static function fieldAsKey(array $array, string $field, bool $unique = false): array
    {
        $result = [];
        foreach ($array as $item) {
            if (isset($item[$field])) {
                if (!$unique && isset($result[$item[$field]])) {
                    $unique                  = true;
                    $result[$item[$field]]   = [($result[$item[$field]])];
                    $result[$item[$field]][] = $item;
                } elseif ($unique) {
                    $result[$item[$field]][] = $item;
                } else {
                    $result[$item[$field]] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * 数组转字符串去重复
     *
     * @param array $data
     *
     * @return string[]
     */
    public static function unique(array $data): array
    {
        return array_unique(explode(',', implode(',', $data)));
    }

    /**
     * 获取数组中去重复过后的指定key值
     *
     * @param array  $list
     * @param string $key
     *
     * @return array
     */
    public static function getUniqueKey(array $list, string $key): array
    {
        return array_unique(array_column($list, $key));
    }

    /**
     * 合并二维数组，并且指定key去重, 第一个覆盖第二个
     *
     * @param array  $arr1
     * @param array  $arr2
     * @param string $key
     *
     * @return array
     */
    public static function mergeArray(array $arr1, array $arr2, string $key): array
    {
        $arr     = array_merge($arr1, $arr2);
        $tmp_arr = [];
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        return $arr;
    }

    /**
     * 相同键值的合并作为键生成新数组
     *
     * @param array  $data
     * @param string $field
     *
     * @return array
     */
    public static function groupSameField(array $data, string $field): array
    {
        $result = [];
        foreach ($data as $key => $info) {
            $result[$info[$field]][] = $info;
        }
        return $result;
    }

    /**
     * 生成无限级树算法
     *
     * @param array      $arr         输入数组
     * @param int|string $pid         根级的pid
     * @param string     $column_name 列名,id|pid父id的名字|children子数组的键名
     *
     * @return array  $ret
     */
    public static function makeTree(array $arr, int|string $pid = 0, string $column_name = 'id|pid|children'): array
    {
        list($idname, $pidname, $cldname) = explode('|', $column_name);
        $ret = array();
        foreach ($arr as $k => $v) {
            if ($v [$pidname] == $pid) {
                $tmp = $arr [$k];
                unset($arr [$k]);
                $tmp [$cldname] = self::makeTree($arr, $v [$idname], $column_name);
                $ret []         = $tmp;
            }
        }
        return $ret;
    }

    /**
     * 二位数组按某个键值排序
     *
     * @param array  $arr
     * @param string $key
     * @param int    $sort
     *
     * @return array
     */
    public static function sortArray(array $arr, string $key, int $sort = SORT_ASC): array
    {
        array_multisort(array_column($arr, $key), $sort, $arr);
        return $arr;
    }

    /**
     * 数组中根据某一列中某个字段的值来查询这一列数据
     *
     * @param array      $array
     * @param string|int $column
     * @param mixed      $value
     *
     * @return array
     */
    public static function getArrayByColumn(array $array, string|int $column, mixed $value): array
    {
        $result = [];
        foreach ($array as $key => $item) {
            if ($item[$column] == $value) {
                $result = $item;
            }
        }
        return $result;
    }

    /**
     * 数组中根据key值获取value
     *
     * @param $array
     * @param $key
     *
     * @return mixed|string
     */
    public static function getConfigValue($array, $key): mixed
    {
        foreach ($array as $item) {
            if ($item['key'] === $key) {
                return $item['value'];
            }
        }
        return '';
    }

}


