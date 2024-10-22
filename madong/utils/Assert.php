<?php

namespace madong\utils;

use madong\exception\ApiException;

/**
 * 断言帮助类
 *
 * @author Mr.April
 * @since  1.0
 */
class Assert
{

    /**
     * 断言表达式为false
     *
     * @param bool   $expression
     * @param string $message
     *
     * @throws \madong\exception\ApiException
     */
    public static function isTrue(bool $expression, string $message = "[Assertion failed] - this expression must be true"): void
    {
        if (!$expression) {
            throw new ApiException($message);
        }
    }

    /**
     * 断言表达式为True
     *
     * @param bool   $expression
     * @param string $message
     *
     * @throws \madong\exception\ApiException
     */
    public static function notTrue(bool $expression, string $message = "[Assertion failed] - this expression must be true"): void
    {
        if ($expression) {
            throw new ApiException($message);
        }
    }

    /**
     * 断言给定的object对象为空
     *
     * @param \helper\mixed $object |null $object
     * @param string        $message
     *
     * @throws \madong\exception\ApiException
     */
    public static function isNull(mixed $object, string $message = "[Assertion failed] - the object argument must be null"): void
    {
        if (!empty($object)) {
            throw new ApiException($message);
        }
    }

    /**
     * 断言给定的object对象为非空
     *
     * @param object|string|null $object
     * @param string             $message
     *
     * @throws \madong\exception\ApiException
     */
    public static function notNull(mixed $object, string $message = "[Assertion failed] - this argument is required; it must not be null"): void
    {
        if (empty($object)) {
            throw new ApiException($message);
        }
    }

    /**
     * 断言给定的字符串为非空
     *
     * @param string|null $str
     * @param string      $message
     *
     * @throws \madong\exception\ApiException
     */
    public static function notEmpty(string|null $str, string $message = "[Assertion failed] - this argument is required; it must not be null or empty"): void
    {
        if ($str == null || strlen($str) == 0) {
            throw new ApiException($message);
        }
    }
}
