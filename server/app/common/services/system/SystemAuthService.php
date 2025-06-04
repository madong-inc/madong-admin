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

namespace app\common\services\system;

use app\common\dao\system\SystemDeptDao;
use app\common\dao\system\SystemTenantPackageMenuDao;
use app\common\dao\system\SystemUserDao;
use app\common\model\system\SystemMenu;
use app\common\model\system\SystemRoleDept;
use app\common\model\system\SystemUser;
use app\common\scopes\global\AccessScope;
use madong\basic\BaseService;
use madong\exception\AuthException;
use madong\helper\Dict;
use madong\helper\Tree;
use madong\interface\IDict;
use madong\services\cache\CacheService;
use madong\utils\JwtAuth;
use support\Container;
use support\Request;

class SystemAuthService extends BaseService
{

    public function __construct(SystemUserDao $dao)
    {
        $this->dao = $dao;
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
            if (empty($token) || $token === 'undefined') {
                throw new AuthException('Token is required.', $code);
            }
            $jwtAuth = Container::make(JwtAuth::class);
            [$id, $type] = $jwtAuth->parseToken($token);
            $prefix   = "token_" . $type . '_';
            $jwtCache = $cache->get($prefix . md5($token));
            if (!$jwtCache) {
                throw new AuthException('登录状态已过期，需要重新登录md00001', $code);
            }

            $this->checkTenantMode($jwtCache, $code);
            $adminInfo = $this->dao->get($id, ['*'], ['roles', 'posts', 'depts'], '', [AccessScope::class]);
            if (empty($adminInfo) || empty($adminInfo->getData('id'))) {
                throw new AuthException('用户不存在或禁用ad00001', $code);
            }

            $depts = $adminInfo->getData('depts');
            if (!empty($depts)) {
                $adminInfo->set('dept_name', $depts->getData('name'));
            }

            $roles = $adminInfo->getData('roles');
            if (!empty($roles)) {
                $adminInfo->set('role_names', array_column($roles->toArray(), 'name'));
            }
            $adminInfo->set('login_date', date('Y-m-d H:i:s', $adminInfo->getData('login_time')));
            $adminInfo->set('type', $type);
            $avatar = $this->getAvatarUrl($adminInfo);
            $adminInfo->set('avatar', $avatar);

            //数据权限构造
            $this->dataAuth($adminInfo);
            return $adminInfo->makeVisible('tenant_id')->makeHidden(['password', 'delete_time', 'status'])->toArray();
        } catch (\Throwable $e) {
            throw new AuthException($e->getMessage(), 401);
        }
    }

    /**
     * 数据权限【1=>默认所有，2=>自定义数据权限，3=>本部门数据权限，4=>本部门及以下数据权限,5=>本人数据权限】
     *
     * @param \app\common\model\system\SystemUser $adminInfo
     */
    public function dataAuth(SystemUser $adminInfo): void
    {
        $roles        = $adminInfo['roles'] ?? null;
        $depts        = $adminInfo['depts']['id'] ?? 0;
        $isSuperAdmin = (int)$adminInfo['is_super_admin'] ?? 0;
        $dataScope    = [];
        $dataAuth     = [];
        if (!empty($roles)) {
            $dataScope = array_unique(array_column($roles->toArray(), 'data_scope'));
        }
        if ($isSuperAdmin) {
            $dataScope = [1];
        }
        if (!in_array(1, $dataScope)) {
            if (in_array(2, $dataScope)) {
                $dataAuth = array_merge($dataAuth, (new SystemRoleDept())->where('role_id', 2)->pluck('dept_id')->toArray());
            }
            if (in_array(3, $dataScope)) {
                $dataAuth = array_merge($dataAuth, [$depts]);
            }
            if (in_array(4, $dataScope)) {
                $dataAuth = array_merge($dataAuth, (new SystemDeptDao())->getChildIdsIncludingSelf($depts));
            }
        }
        $adminInfo->set('data_auth', $dataAuth);
        $adminInfo->set('data_scope', $dataScope);
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
            if (in_array($rule, [
                '/system/auth/user-info',
                '/system/logout',
                '/system/auth/user-menus',
                '/system/auth/perm-code',
                '/system/dict/get-by-dict-type',
                '/system/message/notify-on-first-login-to-all',
            ])) {
                return true;
            }

            // 获取所有接口类型及对应的接口
            $systemMenuService = Container::make(SystemMenuService::class);
            $allAuth           = $this->getAllAuth($systemMenuService);

            // 权限菜单未添加时放行
            if (isset($allAuth[$method]) && in_array($rule, $allAuth[$method])) {
                // 获取管理员角色权限
                $adminInfo = getCurrentUser(true);
                $roleIds   = $this->getRoleIds($request);
                $menuIds   = $this->getMenuIdsByRoleIds($roleIds, $systemMenuService);
                $menuIds   = $this->getFilteredMenuIds($menuIds, (int)$adminInfo['is_super']);//追加租户管理员权限
                $allAuth   = $this->getAllAuth($systemMenuService, $menuIds);
                if (!isset($allAuth[$method]) || !in_array($rule, $allAuth[$method])) {
                    throw new AuthException('您暂时没有访问权限1', 401);
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage(), 401);
        }
    }

    /**
     * 获取后台管理菜单
     *
     * @param array $where
     *
     * @return array
     */
    public function getAllMenus(array $where = []): array
    {
        $systemMenuService = Container::make(SystemMenuService::class);
        $list              = $systemMenuService->dao->selectList($where, '*', 0, 0, 'sort asc', [], true);

        foreach ($list as $item) {
            $item->set('name', $item->code);
            $item->set('meta', SystemMenu::getMetaAttribute($item));
        }
        $list->makeVisible(['id', 'pid', 'type', 'sort', 'redirect', 'path', 'name', 'meta', 'component']);

        $tree = new Tree($list);
        return $tree->getTree();
    }

    /**
     * 获取用户角色获取对应的菜单
     *
     * @param \madong\interface\IDict $userDict
     *
     * @return array
     */
    public function getMenusByUserRoles(IDict $userDict): array
    {
        try {
            $role          = $userDict->get('roles', []);
            $isTenantAdmin = (int)$userDict->get('is_super', 2);
            $roleIds       = array_column($role, 'id');
            if (empty($roleIds) && $isTenantAdmin == 2) {
                return [];
            }
            $systemRoleServices = new SystemRoleService();
            $menuIds            = $systemRoleServices->getMenuIdsByRoleIds($roleIds);
            $systemMenuService  = Container::make(SystemMenuService::class);
            $filteredMenuIds    = $systemMenuService->filterMenuIds($menuIds); // 菜单 ID 集合
            $filteredMenuIds    = $this->getFilteredMenuIds($filteredMenuIds, $isTenantAdmin);//租户套餐权限
            if (empty($filteredMenuIds)) {
                return [];
            }
            $map1 = [
                'type'    => [1, 2],//目录&菜单类型
                'enabled' => 1,
                'id'      => $filteredMenuIds,
            ];
            return $this->getAllMenus($map1);
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage());
        }
    }

    /**
     * 获取用户角色-菜单权限码
     *
     * @param \madong\interface\IDict $userDict
     *
     * @return array
     */
    public function getCodesByUserRoles(IDict $userDict): array
    {
        try {
            $role          = $userDict->get('roles', []);
            $roleIds       = array_column($role, 'id');
            $isTenantAdmin = (int)$userDict->get('is_super', 2);
            if (empty($roleIds) && $isTenantAdmin == 2) {
                return [];
            }
            $systemRoleServices = new SystemRoleService();
            $menuIds            = $systemRoleServices->getMenuIdsByRoleIds($roleIds);
            $systemMenuService  = Container::make(SystemMenuService::class);
            $filteredMenuIds    = $systemMenuService->filterMenuIds($menuIds); // 菜单 ID 集合
            $filteredMenuIds    = $this->getFilteredMenuIds($filteredMenuIds, $isTenantAdmin);//租户套餐权限
            if (empty($filteredMenuIds)) {
                return [];
            }
            $map1 = [
                ['type', '=', 3],
                ['enabled', '=', 1],
                ['id', 'in', $filteredMenuIds],
            ];
            return $systemMenuService->getColumn($map1, 'code');
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage());
        }
    }

    /**
     * 获取所有权限 || 根据$menuIds获取对应权限
     *
     * @param \app\common\services\system\SystemMenuService $systemMenuService
     * @param array                                         $menuIds
     *
     * @return array
     */
    private function getAllAuth(SystemMenuService $systemMenuService, array $menuIds = []): array
    {
        $map = [['path', '<>', ''], ['type', '<>', 3]];
        if (!empty($menuIds)) {
            $map[] = ['id', 'in', $menuIds];
        }
        $allList = $systemMenuService->getColumn($map, 'path', 'methods');

        $allAuth = [];
        foreach ($allList as $key => $value) {
            $methodKey             = strtolower(trim($key));
            $pathKey               = strtolower(trim(str_replace(' ', '', $value)));
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

    /**
     * 获取头像
     *
     * @param \app\common\model\system\SystemUser $adminInfo
     *
     * @return string
     * @throws \Exception
     */
    private function getAvatarUrl(SystemUser $adminInfo): string
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = Container::make(SystemConfigService::class);

        // 获取站点配置地址
        $url          = $systemConfigService->getConfig('site_url', 'site_setting');
        $isDefaultUrl = false;

        // 处理未配置站点地址的默认逻辑
        if (empty($url)) {
            $listenUrl    = config('process.webman.listen');
            $parsedUrl    = parse_url($listenUrl);
            $port         = $parsedUrl['port'] ?? 8787;
            $url          = 'http://127.0.0.1:' . $port;
            $isDefaultUrl = true;
        }
        // 协议型URL处理逻辑
        if (preg_match('#^https?://#i', $url)) {
            // 移除末尾斜杠并标准化路径
            $url = rtrim($url, '/');
            // 获取头像路径
            $avatar = !empty($adminInfo->getData('avatar')) ? $adminInfo->getData('avatar') : '/upload/avatar.jpg';
            $avatar = ltrim($avatar, '/'); // 确保路径相对性
            return $url . '/' . $avatar;
        }

        // 非协议型地址保持原样拼接
        $avatar = $adminInfo->getData('avatar') ?? '/upload/avatar.jpg';
        return rtrim($url, '/') . '/' . ltrim($avatar, '/');
    }

    /**
     * 通过客户端ID匹配数据源
     *
     * @param array $jwtCache
     * @param       $code
     */
    public function checkTenantMode(array $jwtCache, $code): void
    {
        if (config('app.tenant_enabled', false)) {
            $clientId = $this->getClientIdFromRequest() ?? $jwtCache['client_id'];

            if (empty($clientId)) {
                throw new AuthException('登录状态已过期，需要重新登录md00002', $code);
            }
            $tenantInfo = \request()->getTenantId(true);
            if (empty($tenantInfo)) {
                throw new AuthException('登录状态已过期，需要重新登录md00003', $code);
            }
            // 将租户id添加到请求头
            \request()->tenantId = $tenantInfo['tenant_id'];
        }
    }

    /**
     * 获取请求头的客户端id
     *
     * @return string|null
     */
    private function getClientIdFromRequest(): ?string
    {
        return \request()->header('client_id'); // 从请求头获取 client_id
    }

    /**
     * 获取会话的客户端id
     *
     * @param string $prefix
     * @param string $clientId
     *
     * @return mixed
     */
    private function getTenantInfo(string $prefix, string $clientId): mixed
    {
        $cache = Container::make(CacheService::class);
        return $cache->get($prefix . $clientId);
    }

    /**
     * 权限过滤
     *
     * @param array $filteredMenuIds
     * @param int   $isTenantAdmin
     *
     * @return array
     */
    private function getFilteredMenuIds(array $filteredMenuIds, int $isTenantAdmin): array
    {
        $tenantCache = \request()->getTenantId(true);

        if (!config('app.tenant_enabled', false)) {
            //非租户模式直接输出
            return $filteredMenuIds;
        }

        $packageMenu = Container::make(SystemTenantPackageMenuDao::class);
        if ($isTenantAdmin === 3) {
            //租户管理员输出套餐内所有权限
            return $packageMenu->getColumn(['package_id' => $tenantCache['package_id']], 'menu_id');
        } else {
            //租户普通成员输出套餐内权限跟角色权限的差集
            $tenantMenuIds = $packageMenu->getColumn(['package_id' => $tenantCache['package_id']], 'menu_id');
            //默认租户直接返回角色的权限
            if ((int)$tenantCache['is_default'] === 1) {
                return $filteredMenuIds;
            }
            //非默认租户返回角色权限跟套餐的交集
            return array_values(array_intersect($filteredMenuIds, $tenantMenuIds));
        }
    }
}
