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
    private static ?ArrayObject $contextStorage = null;

    private static function initStorage(): void
    {
        if (self::$contextStorage === null) {
            self::$contextStorage = new ArrayObject();
        }
    }

    public static function get(?string $name = null, mixed $default = null): mixed
    {
        self::initStorage();
        if ($name === null) {
            return self::$contextStorage->getArrayCopy();
        }

        return self::$contextStorage->offsetExists($name)
            ? self::$contextStorage->offsetGet($name)
            : $default;
    }

    public static function set(string $name, mixed $value): void
    {
        self::initStorage();

        $allowedKeys = [
            'code',
            'tenant_id',
            'isolation_mode',
            'database_connection',
            'expiration_time',
        ];

        if (!in_array($name, $allowedKeys, true)) {
            throw new \InvalidArgumentException("不允许的上下文键: {$name}");
        }

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

    public static function has(string $name): bool
    {
        self::initStorage();
        return self::$contextStorage->offsetExists($name);
    }

    public static function reset(?ArrayObject $data = null): void
    {
        if ($data !== null) {
            self::$contextStorage = $data;
        } else {
            self::$contextStorage = new ArrayObject();
        }
    }

    public static function destroy(): void
    {
        self::$contextStorage = null;
    }

    public static function setContext(string|int $tenantId, ?string $code, string|int $isolationMode, ?string $connect = null, ?int $expirationTime = null): void
    {
        self::set('tenant_id', $tenantId);
        self::set('code', $code);
        self::set('isolation_mode', (int)$isolationMode);

        if ((int)$isolationMode == IsolationMode::LIBRARY_ISOLATION->value) {
            if ($connect === null) {
                throw new \InvalidArgumentException('库隔离模式必须指定数据库连接配置');
            }
            self::set('database_connection', $connect);
        } else {
            self::set('database_connection', config('database.default'));
        }

        // 设置租户有效期
        self::set('expiration_time', $expirationTime);
    }

    public static function getTenantId(): ?string
    {
        return self::get('tenant_id');
    }

    public static function getTenantCode(): ?string
    {
        return self::get('code');
    }

    public static function getIsolationMode(): string|int
    {
        return self::get('isolation_mode');
    }

    public static function getDatabaseConnection(): ?string
    {
        return self::get('database_connection');
    }

    public static function clear(): void
    {
        self::reset();
    }

    public static function isFieldIsolation(): bool
    {
        return self::get('isolation_mode') === IsolationMode::FIELD_ISOLATION->value;
    }

    public static function isLibraryIsolation(): bool
    {
        return self::get('isolation_mode') === IsolationMode::LIBRARY_ISOLATION->value;
    }

    public static function getExpirationTime(): ?int
    {
        return self::get('expiration_time');
    }

    public static function isExpired(): bool
    {
        $expirationTime = self::getExpirationTime();
        // 如果过期时间为null，表示长期有效，返回false（未过期）
        if ($expirationTime === null) {
            return false;
        }
        // 否则检查当前时间是否超过过期时间
        return time() > $expirationTime;
    }

    private static function validateIsolationMode(string|int $mode): void
    {
        if (!in_array($mode, IsolationMode::valuesArray(), true)) {
            throw new \InvalidArgumentException("无效的隔离模式: {$mode}");
        }
    }
}
