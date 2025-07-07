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

namespace app\middleware;

use app\common\model\system\SysRateLimiter;
use app\common\model\system\SysRateRestrictions;
use app\common\services\system\SysRetaLimiterService;
use app\common\services\system\SysRetaRestrictionsService;
use madong\admin\services\cache\CacheService;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use Webman\RateLimiter\Limiter;
use Webman\RateLimiter\RateLimitException;

/**
 * 限访限流中间件
 *
 * @author Mr.April
 * @since  1.0
 */
class RateLimiterMiddleware implements MiddlewareInterface
{

    /**
     * process
     *
     * @param \Webman\Http\Request $request
     * @param callable             $handler
     *
     * @return \Webman\Http\Response
     */
    public function process(Request $request, callable $handler): Response
    {
        $response = $handler($request);
        $path     = $request->path();
        //跳过对应的安装接口
        if (in_array($path, ['/install/index', '/install/step1', '/install/step2','/'])) {
            return $response;
        }
        $ip     = $request->getRealIp();
        $method = $request->method();

        //限访处理
        $rateRestrictions = $this->getRateRestrictions($ip, $path, $method);
        if (!empty($rateRestrictions)) {
            throw new RateLimitException($rateRestrictions['message'] ?? '拒绝访问');
        }

        $rateRule = $this->getRateLimit($path, $method);
        //限流处理
        if (!empty($rateRule)) {
            $key = $ip . $method . $path;
            Limiter::check(md5($key), $rateRule['limit_value'], $rateRule['ttl'] ?? 1, $rateRule['message']);
            //可以实现更多自定义的限流规则
        }

        return $response;
    }

    /**
     * 获取匹配的限流规则（缓存优先）
     *
     * @param string $path
     * @param string $methods
     *
     * @return array|null
     */
    protected function getRateLimit(string $path, string $methods = 'GET'): ?array
    {
        $cache = Container::make(CacheService::class);
        $rules = $cache->remember(SysRetaLimiterService::CACHE_KEY, function () {
            return SysRateLimiter::where('enabled', 1)->orderBy('priority', 'desc')->get()->toArray();
        });
        if (empty($rules)) {
            return null;
        }
        foreach ($rules as $rule) {
            if ($rule['match_type'] === 'exact' && $rule['path'] === $path && $rule['methods'] === $methods) {
                return $rule;
            }
        }
        return null;
    }

    /**
     * 获取限访规则
     *
     * @param string $ip
     * @param string $path
     * @param string $methods
     *
     * @return array|null
     */
    protected function getRateRestrictions(string $ip, string $path, string $methods = 'GET'): ?array
    {
        $currentTime = time();
        $cache       = Container::make(CacheService::class);
        $rules       = $cache->remember(SysRetaRestrictionsService::CACHE_KEY, function () {
            return SysRateRestrictions::where('enabled', 1)->orderBy('priority', 'desc')->get()->toArray();
        });

        if (empty($rules)) {
            return null;
        }

        foreach ($rules as $rule) {
            if ($rule['ip'] !== $ip || $rule['path'] !== $path || $rule['methods'] !== $methods) {
                continue;
            }
            $hasStartTime = isset($rule['start_time']) && $rule['start_time'] > 0;
            $hasEndTime   = isset($rule['end_time']) && $rule['end_time'] > 0;

            // 规则未生效（未来时间）或已过期
            if (($hasStartTime && $currentTime < $rule['start_time']) ||
                ($hasEndTime && $currentTime > $rule['end_time'])) {
                continue;
            }

            return $rule;
        }
        return null;
    }

}
