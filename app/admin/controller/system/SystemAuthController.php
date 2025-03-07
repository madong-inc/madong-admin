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
use app\admin\validate\system\SystemAuthValidate;
use app\services\system\SystemAuthService;
use app\services\system\SystemRoleMenuService;
use app\services\system\SystemUserRoleService;
use app\services\system\SystemUserService;
use madong\exception\AuthException;
use madong\helper\Dict;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemAuthController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemAuthService::class);
        $this->validate = Container::make(SystemAuthValidate::class);
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
            $data = $this->adminInfo;
            if (empty($data)) {
                throw new AuthException('用户凭证失效请重新登录', 401);
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
    public function getUserMenus(Request $request): \support\Response
    {
        try {
            $dict = new Dict();
            $dict->putAll($this->adminInfo);
            $isSuperAdmin = $dict->get('is_super', 2);
            if ($isSuperAdmin === 1) {
                $map1 = [
                    'type'    => [1, 2], // 目录 & 菜单类型
                    'enabled' => 1,
                ];
                $data = $this->service->getAllMenus($map1);
            } else {
                $data = $this->service->getMenusByUserRoles($dict);
            }
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
            $dict->putAll($this->adminInfo);
            $isSuperAdmin = $dict->get('is_super', 2);
            if ($isSuperAdmin === 1) {
                // 1.如果是超级管理员获取所有按钮集合
                $data = ['admin'];
            } else {
                // 2.根据用户角色获取对应按钮集合
                $data = $this->service->getCodesByUserRoles($dict);
            }
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
            $roleId                = $request->input('role_id');
            $systemRoleMenuService = Container::make(SystemRoleMenuService::class);
            $data                  = $systemRoleMenuService->getColumn(['role_id' => $roleId], 'menu_id');
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
            $systemRoleMenuService = Container::make(SystemRoleMenuService::class);
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
            $systemUserService = Container::make(SystemUserService::class);
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
            $systemUserRoleService = Container::make(SystemUserRoleService::class);
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
            $data                  = $request->all();
            $systemUserRoleService = Container::make(SystemUserRoleService::class);
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
            $systemUserService = Container::make(SystemUserService::class);
            $data              = $systemUserService->getUsersExcludingRole($where, $field, $page, $limit);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

}
