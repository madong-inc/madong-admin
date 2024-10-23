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

namespace app\services\system;

use app\dao\system\SystemUserDao;
use madong\basic\BaseService;
use madong\exception\AuthException;
use madong\service\cache\CacheService;
use madong\utils\Dict;
use madong\utils\JwtAuth;
use support\Container;
use support\Request;

class SystemAuthService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUserDao::class);
    }

    /**
     * 通过token 获取用户信息
     *
     * @param string $token
     * @param int    $code
     *
     * @return array
     */
    public function parseToken(string $token, int $code = 401): array
    {
        try {
            $cache = Container::make(CacheService::class);
            if (!$token || $token === 'undefined') {
                throw new AuthException('', $code);
            }
            $jwtAuth = Container::make(JwtAuth::class);
            [$id, $type] = $jwtAuth->parseToken($token);
            $prefix = "token_" . $type . '_';
            if (!$cache->get($prefix . md5($token))) {
                throw new AuthException('登录状态已过期，需要重新登录md00001', $code);
            }

            $adminInfo = $this->dao->get($id, ['*'], ['roles', 'posts', 'depts']);
            if (empty($adminInfo) || empty($adminInfo->getData('id'))) {
                throw new AuthException('用户不存在或禁用ad00001', $code);
            }
            $adminInfo->set('type', $type);
            return $adminInfo->hidden(['password', 'delete_time', 'status'])->toArray();
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage(), 401);
        }
    }

    /**
     * 权限验证
     *
     * @param \support\Request $request
     *
     * @return bool
     */
    public function verifyAuth(Request $request): bool
    {
        try {
            // 获取当前的接口和接口类型
            $rule   = $request->path();
            $method = strtolower(trim($request->method()));

            // 判断接口是特定几种时放行
            if (in_array($rule, ['/system/user_info', '/system/logout', '/system/permissions', '/system/button-permissions'])) {
                return true;
            }

            // 获取所有接口类型及对应的接口
            $systemMenuService = Container::make(SystemMenuService::class);
            $allAuth           = $this->getAllAuth($systemMenuService);

            // 权限菜单未添加时放行
            if (!isset($allAuth[$method]) || !in_array($rule, $allAuth[$method])) {
                // 获取管理员角色权限
                $roleIds = $this->getRoleIds($request);
                $menuIds = $this->getMenuIdsByRoleIds($roleIds, $systemMenuService);
                $allAuth = $this->getAllAuth($systemMenuService, $menuIds);
                if (!isset($allAuth[$method]) || !in_array($rule, $allAuth[$method])) {
                    throw new AuthException('您暂时没有访问权限', 401);
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage(), 401);
        }
    }

    /**
     * 获取所有权限 || 根据$menuIds获取对应权限
     *
     * @param \app\service\system\SystemMenuService $systemMenuService
     * @param array                                 $menuIds
     *
     * @return array
     */
    private function getAllAuth(SystemMenuService $systemMenuService, array $menuIds = []): array
    {
        $map = [['path', '<>', ''], ['type', '<>', 3]];
        if (!empty($menuIds)) {
            $map[] = ['id', 'in', $menuIds];
        }
        $allList = $systemMenuService->getColumn($map, 'path,methods');

        $allAuth = [];
        foreach ($allList as $item) {
            $methodKey             = strtolower(trim($item['methods']));
            $pathKey               = strtolower(trim(str_replace(' ', '', $item['path'])));
            $allAuth[$methodKey][] = $pathKey;
        }

        return $allAuth;
    }

    /**
     * 通过用户角色筛选ID
     *
     * @param \support\Request $request
     *
     * @return array
     */
    private function getRoleIds(Request $request): array
    {
        $dict = new Dict();
        $dict->putAll($request->adminInfo());
        $roles = $dict->get('roles', []);
        return array_column($roles, 'id');
    }

    /**
     * 获取菜单ids
     *
     * @param array                                 $roleIds
     * @param \app\service\system\SystemMenuService $systemMenuService
     *
     * @return array
     */
    private function getMenuIdsByRoleIds(array $roleIds, SystemMenuService $systemMenuService): array
    {
        $systemRoleService = new SystemRoleService();
        $data              = $systemRoleService->getMenuIdsByRoleIds($roleIds);
        return $systemMenuService->filterMenuIds($data);
    }
}
