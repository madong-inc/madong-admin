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

namespace madong\services\cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheService
{
    private AdapterInterface $cache;
    private string $namespace; // 命名空间
    private string $prefix; // 默认前缀
    private string $adapter; // 缓存适配器类型
    private ?\Redis $redis; // Redis 实例

    // 构造函数，初始化缓存服务
    public function __construct(array $options = [])
    {
        // 获取框架的 Redis 配置
        $options = array_merge([
            'host'     => '127.0.0.1',
            'password' => null,
            'port'     => 6379,
            'database' => 0,
        ], config('redis.default', []));

        // 获取扩展配置信息
        $config = array_merge([
            'namespace' => '',
            'type'      => 'file',
            'prefix'    => '',
        ], config('madong.cache', []));

        $this->adapter   = $config['type'] ?? 'file';
        $this->prefix    = $config['prefix'] ?? '';
        $this->namespace = $config['namespace'] ?? '';
        $this->redis     = null;

        $this->initialize($options);
    }

    private function initialize(array $options): void
    {
        switch ($this->adapter) {
            case 'redis':
                $this->redis = new \Redis();
                $this->redis->connect($options['host'], $options['port'] ?? 6379); // 连接到 Redis
                $this->cache = new RedisAdapter($this->redis, $this->namespace);
                break;
            case 'file':
            default:
                $this->cache = new FilesystemAdapter($this->prefix);
                break;
        }
    }

    // 读取缓存
    public function get(string $key, $default = null)
    {
        $item = $this->cache->getItem($this->prefix . $key);
        return $item->isHit() ? $item->get() : $default;
    }

    // 写入缓存
    public function set(string $key, $value, int $ttl = 3600)
    {
        $item = $this->cache->getItem($this->prefix . $key);
        $item->set($value);
        $item->expiresAfter($ttl);
        $this->cache->save($item);
    }

    // 读取缓存没有则回调查询
    public function remember(string $key, callable $callback, int $ttl = 3600)
    {
        $value = $this->get($key);
        if ($value === null) {
            $value = $callback();
            $this->set($key, $value, $ttl);
        }
        return $value;
    }

    // 删除
    public function delete(string $key)
    {
        $this->cache->deleteItem($this->prefix . $key);
    }

    // 清空缓存
    public function clear(string $prefix = ''): void
    {
        $this->clearByPrefix($prefix);
    }

    private function clearByPrefix(string $prefix): void
    {
        if ($this->adapter === 'redis' && $this->redis) {
            if (!empty($this->namespace)) {
                $prefix = $this->namespace . ':' . $prefix;
            }
            $keys = $this->redis->keys($prefix . '*'); // 查找所有匹配的键
            if (!empty($keys)) {
                $this->redis->del($keys); // 删除匹配的键
            }
        } else {
            $this->cache->clearPrefix($prefix); // 对于文件系统适配器，使用 clearPrefix
        }
    }

    // 设置锁
    public function setLock(string $lockKey, int $ttl = 30): bool
    {
        if ($this->redis) {
            return $this->redis->set($lockKey, 'locked', ['nx', 'ex' => $ttl]);
        }
        return false; // Redis 未初始化，返回 false
    }

    // 释放锁
    public function releaseLock(string $lockKey): void
    {
        if ($this->redis) {
            $this->redis->del($lockKey);
        }
    }

    // 检查锁
    public function checkLock(string $lockKey): bool
    {
        return $this->redis && $this->redis->exists($lockKey) === 1;
    }

    // 检查键是否存在
    public function keyExists(string $key): bool
    {
        $item = $this->cache->getItem($this->prefix . $key);
        return $item->isHit();
    }
}
