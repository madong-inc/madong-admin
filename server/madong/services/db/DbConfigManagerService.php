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

namespace madong\services\db;

use PDO;
use Webman\Exception\BusinessException;

class DbConfigManagerService
{
    private const CACHE_KEY = 'db_config_cache';
    private const CACHE_TTL = 300; // 5分钟缓存

    private const TABLE_NAME = 'ma_system_dc_tenants';//数据中心表名

    // 静态调用入口
    public static function getConfig(array $base = [], string $defaultKey = 'mysql', bool $isDefaultAppend = false): array
    {
        // 尝试从缓存获取 优化无需缓存载入过后不会再获取配置数据
//        $config = Cache::get(self::CACHE_KEY);
//        if ($config !== null) {
//            return $config;
//        }
        try {
            if (empty($base)) {
                throw new BusinessException('Missing database name in base config');
            }
            $config = self::queryDbConfig($base);
            if ($isDefaultAppend && !empty($defaultKey)) {
                $config[$defaultKey] = $base;
            }
            //无效缓存配置程序启动载入后不在载入
//            Cache::set(self::CACHE_KEY, $config, self::CACHE_TTL);
            return $config;
        } catch (\PDOException $e) {
            throw new BusinessException('数据库配置获取失败: ' . $e->getMessage());
        }
    }

    private static function queryDbConfig(array $base): array
    {
        $config = [];
        $dsn    = sprintf('mysql:host=%s;port=%d;dbname=%s',
            $base['host'],
            $base['port'],
            $base['database']
        );
        $pdo    = new PDO(
            $dsn,
            $base['username'],
            $base['password'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'",
            ]
        );

        $table = self::TABLE_NAME;
        $sql   = "SELECT `db_domain`, `db_host`, `db_port`, `db_name`, `db_user`, `db_password` 
                FROM `{$table}`
                WHERE enabled = 1";
        $stmt  = $pdo->prepare($sql);
        $stmt->execute();
        $tenants = $stmt->fetchAll();

        foreach ($tenants as $tenant) {
            $config[$tenant['db_domain']] = [
                'driver'      => 'mysql',
                'host'        => $tenant['db_host'],
                'port'        => (int)$tenant['db_port'],
                'database'    => $tenant['db_name'],
                'username'    => $tenant['db_user'],
                'password'    => $tenant['db_password'],
                'charset'     => 'utf8mb4',
                'unix_socket' => $base['unix_socket'] ?? '',
                'collation'   => 'utf8mb4_general_ci',
                'prefix'      => $base['prefix'] ?? '',
                'strict'      => $base['strict'] ?? false,
                'engine'      => null,
                'pool'        => $base['pool'] ?? [],
            ];
        }
        $pdo = null;
        return $config;
    }
}
