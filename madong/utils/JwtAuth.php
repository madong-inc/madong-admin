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
    public function getToken(int $id, string $username, string $type, array $params = []): array
    {
        $host          = request()->host();
        $time          = time();
        $hour          = config('bolt.cross.token_expire', 6);
        $params        += [
            'iss' => $host,
            'aud' => $host,
            'iat' => $time,
            'nbf' => $time,
            'exp' => strtotime('+ ' . $hour . ' hour'),
        ];
        $params['jti'] = compact('id', 'type');
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
     * 创建token
     *
     * @param int    $id
     * @param string $username
     * @param string $type
     * @param array  $params
     *
     * @return array
     */
    public function createToken(int $id, string $username, string $type, array $params = []): array
    {
        $cache     = Container::make(CacheService::class);
        $tokenInfo = $this->getToken($id, $username, $type, $params);
        $exp       = $tokenInfo['params']['exp'] - $tokenInfo['params']['iat'] + 60;
        $prefix    = 'token_' . $type . '_';
        $cache->set($prefix . md5($tokenInfo['token']), ['uid' => $id, 'type' => $type, 'token' => $tokenInfo['token'], 'exp' => $exp], (int)$exp);
        return $tokenInfo;
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
