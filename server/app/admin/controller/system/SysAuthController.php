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

namespace app\admin\controller\system;

use app\admin\controller\Crud;
use app\admin\validate\system\SysAuthValidate;
use app\common\model\system\SysRole;
use app\common\services\system\SysRoleService;
use madong\admin\context\TenantContext;
use app\common\scopes\global\TenantScope;
use app\common\services\platform\TenantService;
use app\common\services\platform\TenantSessionService;
use app\common\services\system\SysAdminRoleService;
use app\common\services\system\SysAdminService;
use app\common\services\system\SysAdminTenantService;
use app\common\services\system\SysAuthService;
use app\common\services\system\SysMenuService;
use app\common\services\system\SysRoleMenuService;
use app\common\services\system\SysRoleScopeDeptService;
use madong\admin\ex\AuthException;
use madong\admin\utils\Json;
use madong\exception\handler\UnauthorizedHttpException;
use madong\helper\Dict;
use madong\jwt\JwtToken;
use support\Container;
use support\Request;

class SysAuthController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        /** @var  SysAuthService $service */
        $service = Container::make(SysAuthService::class);
        /** @var SysAuthValidate $validate */
        $validate       = Container::make(SysAuthValidate::class);
        $this->service  = $service;
        $this->validate = $validate;
    }

    /**
     * 获取登录用户
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getUserInfo(Request $request): \support\Response
    {
        try {
            $data = getCurrentUser(true, true);
            if (empty($data)) {
                throw new UnauthorizedHttpException('用户凭证失效请重新登录', 401);
            }
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), null, 401);
        }
    }

    /**
     * 获取用户-权限菜单
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getPermissionsMenu(Request $request): \support\Response
    {
        try {
            $dict = new Dict();
            $dict->putAll(getCurrentUser(true, true));
            $data = $this->service->getMenusByUserRoles($dict);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 权限菜单-包含所有 菜单 按钮  接口
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getPermissions(Request $request): \support\Response
    {
        try {
            $dict = new Dict();
            $dict->putAll(getCurrentUser(true, true));
            $data = $this->service->getMenusByUserRoles($dict, true);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 获取用户-权限码
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getUserCodes(Request $request): \support\Response
    {
        try {
            $dict = new Dict();
            $dict->putAll(getCurrentUser(true, true));
            $data = $this->service->getCodesByUserRoles($dict);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 通过角色ID获取权限ID集合
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function roleMenuIds(Request $request): \support\Response
    {
        try {
            $roleId   = $request->input('role_id');
            $tenantId = $request->input('tenant_id', null);
            /** @var  SysRoleService $systemRoleService */
            $systemRoleService = Container::make(SysRoleService::class);
            $data              = $systemRoleService->getPermissionColumns($roleId, $tenantId);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 通过角色ID获取权限范围自定义部门ID集合
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function roleScopeIds(Request $request): \support\Response
    {
        try {
            $roleId                = $request->input('role_id');
            $systemRoleMenuService = Container::make(SysRoleScopeDeptService::class);
            $data                  = $systemRoleMenuService->getColumn(['role_id' => $roleId], 'dept_id');
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }

    }

    /**
     * 保存角色菜单关系
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function saveRoleMenuRelation(Request $request): \support\Response
    {
        try {
            $data                  = $request->all();
            $systemRoleMenuService = Container::make(SysRoleMenuService::class);
            $systemRoleMenuService->save($data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 获取角色-关联用户列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getUsersByRoleId(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $where['role_id']  = $request->input('role_id');
            $systemUserService = Container::make(SysAdminService::class);
            $data              = $systemUserService->getUsersListByRoleId($where, $field, $page, $limit);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }

    }

    /**
     * 保存用户-关联角色
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function saveUserRoles(Request $request): \support\Response
    {
        try {
            $data                  = $request->all();
            $systemUserRoleService = Container::make(SysAdminRoleService::class);
            $systemUserRoleService->saveUserRoles($data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 移除用户-关联角色
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function removeUserRole(Request $request): \support\Response
    {
        try {
            $data = $request->all();
            /** @var  SysAdminRoleService $systemUserRoleService */
            $systemUserRoleService = Container::make(SysAdminRoleService::class);
            $systemUserRoleService->removeUserRole($data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 获取用户列表-排除指定角色id
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getUsersExcludingRole(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $where['role_id']  = $request->input('role_id');
            $systemUserService = Container::make(SysAdminService::class);
            $data              = $systemUserService->getUsersExcludingRole($where, $field, $page, $limit);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 获取用户租户列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getUserTenant(Request $request): \support\Response
    {
        try {
            $id      = getCurrentUser(false);
            $service = new SysAdminService();
            $result  = $service->getAdminById($id);
            if (empty($result)) {
                return Json::success('ok', []);
            }
            $result->current_tenant_id = TenantContext::getTenantId();

            $data                      = $result->toArray();
            $data['current_tenant_id'] = TenantContext::getTenantId();

            if (boolval($result->is_super)) {
                $tenant                  = (new TenantService())->selectList([], '*', 0, 0, '', [], false, [TenantScope::class]);
                $data['managed_tenants'] = $tenant; // 直接在数组中更新
            }

            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 切换租户
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function changeTenant(Request $request): \support\Response
    {
        try {
            $tenantId = $request->input('tenant_id', null);
            if (empty($tenantId)) {
                throw new \Exception('参数异常缺少必须参数');
            }
            $tokenHeader = $request->header(Config('madong.cross.token_name', 'Authorization'), '');
            $token       = '';
            if ($tokenHeader) {
                // 去掉 'Bearer ' 前缀（如果存在）
                $token = trim(ltrim($tokenHeader, 'Bearer'));
            }

            // 6.如果请求头中没有 token，尝试从 GET 参数中获取
            if (empty($token)) {
                $tokenParam = $request->get('token');
                if ($tokenParam) {
                    $token = trim($tokenParam);
                }
            }
            $service = Container::make(TenantSessionService::class);
            $model   = $service->get(['token' => md5($token)], null, ['tenant']);
            if (empty($model)) {
                throw new \Exception('异常请稍后重试');
            }
            $model->tenant_id = $tenantId;
            $model->save();
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 授权租户
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function tenantGrant(Request $request): \support\Response
    {
        try {
            $data  = $request->all();
            $admin = getCurrentUser(true);
            if ($admin['is_super'] !== 1) {
                throw new \Exception('不支持当前操作');
            }
            $service = Container::make(SysAdminTenantService::class);
            $result  = $service->updateAdminTenantRelations($data['admin_id'], $service->buildDefaultTenantData($data['tenant_id']));
            if (!$result) {
                throw new \Exception('授权失败');
            }
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 刷新Token
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \madong\exception\handler\UnauthorizedHttpException
     */
    public function refreshToken(Request $request): \support\Response
    {
        try {
            $data = JwtToken::refreshToken();
            return Json::success('ok', $data['access_token']);
        } catch (\Throwable $e) {
            throw new UnauthorizedHttpException();
        }
    }
}
