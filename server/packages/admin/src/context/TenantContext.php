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

namespace madong\admin\context;

use ArrayObject;
use app\common\enum\platform\IsolationMode;
use Workerman\Coroutine\Context\ContextInterface;

/**
 * 租户上下文管理类
 * 实现 ContextInterface 接口，提供统一的上下文管理能力，
 * 同时支持动态管理租户相关的上下文信息，如租户ID、隔离模式和数据库连接配置。
 */
class TenantContext implements ContextInterface
{
    /**
     * 使用 ArrayObject 作为内部存储结构，以支持引用传递和统一接口。
     *
     * @var ArrayObject|null
     */
    private static ?ArrayObject $contextStorage = null;

    /**
     * 初始化上下文存储。
     * 如果尚未初始化，则创建一个新的 ArrayObject 实例。
     */
    private static function initStorage(): void
    {
        if (self::$contextStorage === null) {
            self::$contextStorage = new ArrayObject();
        }
    }

    /**
     * 获取上下文中的值。
     *
     * @param string|null $name    上下文值的名称。如果为 null，则返回整个上下文数据。
     * @param mixed       $default 如果指定的名称不存在，则返回的默认值。
     *
     * @return mixed 上下文中的值或默认值。
     */
    public static function get(?string $name = null, mixed $default = null): mixed
    {
        self::initStorage();
        if ($name === null) {
            // 如果未指定名称，返回整个上下文数据的副本以避免外部修改内部状态
            return self::$contextStorage->getArrayCopy();
        }

        return self::$contextStorage->offsetExists($name)
            ? self::$contextStorage->offsetGet($name)
            : $default;
    }

    /**
     * 设置上下文中的值。
     *
     * @param string $name  上下文值的名称。
     * @param mixed  $value 要设置的值。
     *
     * @throws \InvalidArgumentException 如果尝试设置不允许的上下文键。
     */
    public static function set(string $name, mixed $value): void
    {
        self::initStorage();

        // 定义允许的上下文键（可选，根据需求决定是否限制）
        // 如果不限制，可以移除此检查
        $allowedKeys = [
            'code',
            'tenant_id',
            'isolation_mode',
            'database_connection',
        ];

        if (!in_array($name, $allowedKeys, true)) {
            throw new \InvalidArgumentException("不允许的上下文键: {$name}");
        }

        // 租户特定逻辑验证（可选）
        if ($name === 'isolation_mode') {
            self::validateIsolationMode($value);
        }

        if ($name === 'isolation_mode' && $value === 'library_isolation') {
            if (!self::$contextStorage->offsetExists('database_connection')) {
                throw new \InvalidArgumentException('库隔离模式必须指定数据库连接配置');
            }
        }

        self::$contextStorage->offsetSet($name, $value);
    }

    /**
     * 检查上下文中是否存在指定名称的值。
     *
     * @param string $name 要检查的上下文值名称。
     *
     * @return bool 如果存在则返回 true，否则返回 false。
     */
    public static function has(string $name): bool
    {
        self::initStorage();
        return self::$contextStorage->offsetExists($name);
    }

    /**
     * 初始化或重置当前协程上下文。
     *
     * @param ArrayObject|null $data 如果提供，则使用提供的 ArrayObject 初始化上下文；否则，清空当前上下文。
     */
    public static function reset(?ArrayObject $data = null): void
    {
        if ($data !== null) {
            // 使用提供的 ArrayObject 初始化上下文
            self::$contextStorage = $data;
        } else {
            // 清空当前上下文
            self::$contextStorage = new ArrayObject();
        }
    }

    /**
     * 销毁上下文，释放资源。
     */
    public static function destroy(): void
    {
        self::$contextStorage = null;
    }

    /**
     * 设置租户上下文（兼容旧方法）
     *
     * @param string|int  $tenantId      租户ID
     * @param string|int  $isolationMode 隔离模式
     * @param string|null $connect       数据库连接配置（仅库隔离模式需要）
     */
    public static function setContext(string|int $tenantId, ?string $code, string|int $isolationMode, ?string $connect = null): void
    {
        // 设置租户ID
        self::set('tenant_id', $tenantId);
        // 设置租户code
        self::set('code', $code);
        // 设置隔离模式
        self::set('isolation_mode', (int)$isolationMode);
        // 如果是库隔离模式，必须指定数据库连接
        if ((int)$isolationMode == IsolationMode::LIBRARY_ISOLATION->value) {
            if ($connect === null) {
                throw new \InvalidArgumentException('库隔离模式必须指定数据库连接配置');
            }
            self::set('database_connection', $connect);
        } else {
            // 字段隔离模式使用默认数据库连接
            self::set('database_connection', config('database.default'));
        }
    }

    /**
     * 获取租户ID（兼容旧方法）
     *
     * @return string|null 租户ID
     */
    public static function getTenantId(): ?string
    {
        return self::get('tenant_id');
    }

    /**
     * 获取租户Code
     *
     * @return string|null
     */
    public static function getTenantCode(): ?string
    {
        return self::get('code');
    }

    /**
     * 获取隔离模式（兼容旧方法）
     *
     * @return string|int 隔离模式
     */
    public static function getIsolationMode(): string|int
    {
        return self::get('isolation_mode');
    }

    /**
     * 获取数据库连接配置（兼容旧方法）
     *
     * @return string|null 数据库连接配置
     */
    public static function getDatabaseConnection(): ?string
    {
        return self::get('database_connection');
    }

    /**
     * 清理上下文（兼容旧方法）
     * 相当于 reset() 不传递任何数据，清空上下文。
     */
    public static function clear(): void
    {
        self::reset();
    }

    /**
     * 检查是否处于字段隔离模式
     *
     * @return bool 如果是字段隔离模式则返回 true，否则返回 false
     */
    public static function isFieldIsolation(): bool
    {
        return self::get('isolation_mode') === IsolationMode::FIELD_ISOLATION->value;
    }

    /**
     * 检查是否处于库隔离模式
     *
     * @return bool 如果是库隔离模式则返回 true，否则返回 false
     */
    public static function isLibraryIsolation(): bool
    {
        return self::get('isolation_mode') === IsolationMode::LIBRARY_ISOLATION->value;
    }

    /**
     * 验证隔离模式是否有效
     *
     * @param string|int $mode 隔离模式
     */
    private static function validateIsolationMode(string|int $mode): void
    {

        if (!in_array($mode, IsolationMode::valuesArray(), true)) {
            throw new \InvalidArgumentException("无效的隔离模式: {$mode}");
        }
    }
}

