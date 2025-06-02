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

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use madong\exception\AuthException;
use madong\services\cache\CacheService;
use support\Container;

/**
 * JWT认证类
 *
 * @author Mr.April
 * @since  1.0
 */
class JwtAuth
{
    /**
     * token
     *
     * @var string
     */
    protected string $token;

    public string $key = 'madong';

    /**
     * 获取token
     *
     * @param int    $id
     * @param string $username
     * @param string $type
     * @param array  $params
     *
     * @return array
     */
    public function getToken(int $id, string $username, string $type, string $clientId = '', array $params = []): array
    {
        $host          = request()->host();
        $time          = time();
        $hour          = config('bolt.cross.token_expire', 6);
        $client_id     = $clientId ?? '';
        $params        += [
            'iss' => $host,
            'aud' => $host,
            'iat' => $time,
            'nbf' => $time,
            'exp' => strtotime('+ ' . $hour . ' hour'),
        ];
        $params['jti'] = compact('id', 'type', 'client_id');
        $token         = JWT::encode($params, $this->key, 'HS256');
        return compact('token', 'params');
    }

    /**
     * 解析token
     *
     * @param string $jwt
     *
     * @return array
     */
    public function parseToken(string $jwt): array
    {
        try {
            $this->token = $jwt;
            list($headb64, $bodyb64, $cryptob64) = explode('.', $this->token);
            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
            return [$payload->jti->id, $payload->jti->type];
        } catch (\Exception $e) {
            throw new AuthException('登录状态已过期，需要重新登录', 401);
        }
    }

    /**
     * 创建tonken
     *
     * @param int    $id
     * @param string $username
     * @param string $type
     * @param array  $tenant
     * @param array  $params
     *
     * @return array
     */
    public function createToken(int $id, string $username, string $type, array $tenant = [], array $params = []): array
    {
        $currentIp = request()->getRemoteIp();
        $uniqueId  = $this->generateUniqueId($currentIp);
        $tokenInfo = $this->getToken($id, $username, $type, md5($uniqueId), $params);
        $exp       = $this->calculateExpiration($tokenInfo);

        // 缓存令牌信息
        $this->cacheToken($tokenInfo, $id, $type, md5($uniqueId), $exp);

        //创建客户端id
        if (config('app.tenant_enabled')) {
            $this->cacheTenant(md5($uniqueId), $tenant, $exp, $type);
        }

        return $tokenInfo;
    }

    private function cacheToken(array $tokenInfo, int $id, string $type, string $uniqueId, int $exp): void
    {
        $cache  = Container::make(CacheService::class);
        $prefix = 'token_' . $type . '_';
        $cache->set($prefix . md5($tokenInfo['token']), [
            'uid'       => $id,
            'type'      => $type,
            'token'     => $tokenInfo['token'],
            'client_id' => $uniqueId,
            'exp'       => $exp,
        ], (int)$exp);
    }

    private function cacheTenant(string $uniqueId, array $tenant, int $exp, string $type): void
    {
        $cache  = Container::make(CacheService::class);
        $prefix = 'tenant_';
        $cache->set($prefix . $uniqueId, $tenant, (int)$exp);
    }

    private function generateUniqueId(string $currentIp): string
    {
        return uniqid($currentIp . '-', true);
    }

    private function calculateExpiration(array $tokenInfo): int
    {
        return $tokenInfo['params']['exp'] - $tokenInfo['params']['iat'] + 60;
    }

    /**
     * 密码哈希
     *
     * @param        $password
     * @param string $algo
     *
     * @return false|string|null
     */
    public static function passwordHash($password, string $algo = PASSWORD_DEFAULT): bool|string|null
    {
        return password_hash($password, $algo);
    }

    /**
     * 验证密码哈希
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public static function passwordVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

}
