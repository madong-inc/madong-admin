<?php
declare(strict_types=1);
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

namespace app\adminapi;

use app\model\system\Admin;
use app\scope\global\AccessPermissionScope;
use app\service\admin\system\AdminService;
use core\cache\CacheService;
use core\exception\handler\ForbiddenHttpException;
use core\jwt\JwtToken;
use core\logger\Logger;
use madong\swagger\attribute\Permission;

final  readonly class CurrentUser
{
    // 缓存键前缀
    public const CACHE_PREFIX = 'admin_user';

    // 缓存过期时间（秒）
    public const CACHE_EXPIRE = 3600;

    private AdminService $service;
    private CacheService $cache;

    public function __construct(AdminService $service, CacheService $cache)
    {
        $this->service = $service;
        $this->cache = $cache;
    }

    /**
     * 生成管理员缓存键
     * @param int|string $uid 管理员ID
     * @return string 缓存键
     */
    public static function generateCacheKey(int|string $uid): string
    {
        return self::CACHE_PREFIX . '_' . $uid;
    }

    public function admin(bool $toArray = false): null|Admin|array
    {
        if (!$this->getToken()) {
            return null;
        }
        $uid = $this->id();
        $cacheKey = self::generateCacheKey($uid);

        // 使用缓存服务获取管理员信息
        $admin = $this->cache->remember($cacheKey,  function () use ($uid) {
            return $this->service->get($uid, ['*'], [], '', [AccessPermissionScope::class]);
        }, self::CACHE_EXPIRE);

        return $toArray && $admin ? $admin->toArray() : $admin;
    }

    /**
     * 清除管理员缓存
     * @param int|string|null $uid 管理员ID，默认清除当前用户缓存
     */
    public function clearCache(int|string|null $uid = null): void
    {
        $uid = $uid ?? $this->id();
        if ($uid) {
            $cacheKey = self::generateCacheKey($uid);
            $this->cache->delete($cacheKey);
            
            // 同时清除权限缓存
            $permissionCacheKey = $cacheKey . '_permissions';
            $this->cache->delete($permissionCacheKey);
        }
    }

    public function refresh(): array
    {
        if (!$this->getToken()) {
            Logger::debug("当前用户无有效 Token，无法刷新");
            return [];
        }
        return (new JwtToken())->refresh()->toArray();
    }

    public function id(): int|string
    {
        $token = $this->getToken();
        if (!$token) {
            return 0;
        }
        $aid = (new JwtToken())->id();
        if ($aid === null) {
            return 0;
        }
        return $aid ?? 0;
    }

    public function isSuperAdmin(): bool
    {
        $admin = $this->admin();
        return $admin && $admin->isSuperAdmin();
    }

    public function getToken(): ?string
    {
        $request = request();
        if (empty($request)) {
            return null;
        }
        $tokenName     = config('core.jwt.app.token_name', 'Authorization');
        $authorization = $request->header($tokenName);
        if (empty($authorization) || $authorization === 'undefined') {
            $authorization = $request->get('token');
        }
        if (!$authorization || $authorization === 'undefined') {
            return null;
        }
        if (count(explode(' ', $authorization)) !== 2) {
            return null;
        }

        [$type, $token] = explode(' ', $authorization);

        if ($type !== 'Bearer') {
            return null;
        }

        if (!$token || $token === 'undefined') {
            return null;
        }

        return $token;
    }

    /**
     * 生成新令牌
     * @param array $userInfo 用户信息
     * @param string $type 类型
     * @return array 令牌信息
     */
    public function generateToken(array $userInfo, string $type = 'admin'): array
    {
        $jwt = new JwtToken();
        $tokenObj = $jwt->generate((string)$this->id(), $type, $userInfo);
        return [
            'access_token' => $tokenObj->accessToken,
            'refresh_token' => $tokenObj->refreshToken,
            'expires_in' => $tokenObj->expiresIn,
            'expires_time' => time() + $tokenObj->expiresIn
        ];
    }

    /**
     * 登出（将令牌加入黑名单）
     * @param string|null $token 令牌，默认使用当前请求的令牌
     * @return bool 是否成功
     */
    public function logout(?string $token = null): bool
    {
        $token = $token ?? $this->getToken();
        if (!$token) {
            return false;
        }
        try {
            (new JwtToken())->logout($token);
            return true;
        } catch (\Exception $e) {
            Logger::error("登出失败: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取令牌负载信息
     * @return array 负载信息
     */
    public function getPayload(): array
    {
        try {
            $jwt = new JwtToken();
            // 使用 getPayloadFromRequest() 方法获取负载信息
            $payload = $jwt->getPayloadFromRequest();
            return $payload['ext'] ?? $payload;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 获取用户所有菜单权限码
     *
     * @return array 权限码数组
     */
    public function getPermissions(): array
    {
        $admin = $this->admin();
        if (!$admin) {
            return [];
        }

        if ($admin->isSuperAdmin()) {
            return ['*']; // 超级管理员拥有所有权限
        }

        $uid = $this->id();
        $cacheKey = self::generateCacheKey($uid) . '_permissions';
        
        // 使用缓存服务获取权限信息
        return $this->cache->remember($cacheKey, function () use ($admin) {
            $permissions = [];
            // 通过角色获取菜单权限码
            $roles = $admin->roles()->with('menus')->get();

            foreach ($roles as $role) {
                foreach ($role->menus as $menu) {
                    if (!empty($menu->code)) {
                        $permissions[] = $menu->code;
                    }
                }
            }

            return array_unique($permissions);
        }, self::CACHE_EXPIRE);
    }

    /**
     * 检查用户是否有权限
     *
     * @param string|array $codes 权限码，可以是字符串或数组
     * @param string $operation 操作类型，支持 'and' 或 'or'
     * @return bool 是否有权限
     */
    public function hasPermission(string|array $codes, string $operation = 'and'): bool
    {
        // 如果是超级管理员，直接返回true
        if ($this->isSuperAdmin()) {
            return true;
        }

        $codes = is_array($codes) ? $codes : [$codes];
        $userPermissions = $this->getPermissions();

        // 如果有通配符权限，直接返回true
        if (in_array('*', $userPermissions)) {
            return true;
        }

        $operation = strtolower($operation);
        
        if ($operation === Permission::OPERATION_AND) {
            // AND模式：所有权限码都必须有
            foreach ($codes as $code) {
                if (!in_array($code, $userPermissions)) {
                    return false;
                }
            }
            return true;
        } else if ($operation === Permission::OPERATION_OR) {
            // OR模式：至少有一个权限码
            foreach ($codes as $code) {
                if (in_array($code, $userPermissions)) {
                    return true;
                }
            }
            return false;
        }

        throw new \InvalidArgumentException("不支持的操作类型: {$operation}");
    }

    /**
     * 检查用户是否有权限，没有权限时抛出异常
     *
     * @param string|array $codes 权限码，可以是字符串或数组
     * @param string $operation 操作类型，支持 'and' 或 'or'
     * @throws ForbiddenHttpException 没有权限时抛出异常
     */
    public function checkPermission(string|array $codes, string $operation = 'and'): void
    {
        if (!$this->hasPermission($codes, $operation)) {
            $codes = is_array($codes) ? $codes : [$codes];
            $codesStr = implode($operation === 'and' ? ',' : '或', $codes);
            throw new ForbiddenHttpException("缺少权限: {$codesStr}");
        }
    }

}