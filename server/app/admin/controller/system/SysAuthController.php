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
use app\common\services\system\SysAdminRoleService;
use app\common\services\system\SysAdminService;
use app\common\services\system\SysAuthService;
use app\common\services\system\SysRoleMenuService;
use app\common\services\system\SysRoleScopeDeptService;
use app\common\services\system\SysRoleService;
use core\jwt\JwtToken;
use core\utils\Json;
use core\exception\handler\UnauthorizedHttpException;
use madong\helper\Dict;
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
            $format = input('format', 'default');
            if ($format == 'mixture') {
                //兼容Art菜单
                return $this->getArtDesignMenu();
            }
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
            $where['role_id'] = $request->input('role_id');
            /** @var SysAdminService $systemUserService */
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
     * 刷新Token
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \core\exception\handler\UnauthorizedHttpException
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

    /**
     * ArtDesignMenu
     *
     * @return \support\Response
     */
    private function getArtDesignMenu(): \support\Response
    {
        try {
            $dict = new Dict();
            $dict->putAll(getCurrentUser(true, true));

            $data = [
                [
                    'name'      => "Dashboard",
                    'path'      => "/dashboard",
                    'component' => "/layout",
                    'meta'      => [
                        'order' => 1,
                        'title' => "menus.dashboard.title",
                        'icon'  => "&#xe721;",
                        'roles' => [
                            "R_SUPER",
                            "R_ADMIN",
                        ],
                    ],
                    'children'  => [
                        [
                            'path'      => "/dashboard/console",
                            'name'      => "Console",
                            'component' => "/dashboard/console",
                            'meta'      => [
                                'title'     => "menus.dashboard.console",
                                'keepAlive' => false,
                                'fixedTab'  => true,
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/dashboard/analysis",
                            'name'      => "Analysis",
                            'component' => "/dashboard/analysis",
                            'meta'      => [
                                'title'     => "menus.dashboard.analysis",
                                'keepAlive' => false,
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/dashboard/ecommerce",
                            'name'      => "Ecommerce",
                            'component' => "/dashboard/ecommerce",
                            'meta'      => [
                                'title'     => "menus.dashboard.ecommerce",
                                'keepAlive' => false,
                            ],
                            'children'  => [],
                        ],
                    ],
                ],
                [
                    'path'      => "/system/manager",
                    'name'      => "system:manager",
                    'component' => "/layout",
                    'meta'      => [
                        'order' => 10,
                        'title' => "系统设置",
                        'icon'  => "ant-design:setting-outlined",
                    ],
                    'children'  => [
                        [
                            'path'      => "/examples/form/index",
                            'name'      => "examples:form",
                            'component' => "/examples/form/index",
                            'meta'      => [
                                'title' => "表单示例",
                                'icon'  => "ant-design:form-outlined",
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/user/index",
                            'name'      => "system:user",
                            'component' => "/system/user/index",
                            'meta'      => [
                                'title'     => "用户管理",
                                'icon'      => "ant-design:user-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/user/online-user-list",
                            'name'      => "system:onlineUserList",
                            'component' => "/system/user/online-user-list",
                            'meta'      => [
                                'title'     => "在线用户",
                                'icon'      => "ant-design:cloud-server-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/role/index",
                            'name'      => "system:role",
                            'component' => "/system/role/index",
                            'meta'      => [
                                'title'     => "角色管理",
                                'icon'      => "ant-design:team-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/menu/index",
                            'name'      => "system:menu",
                            'component' => "/system/menu/index",
                            'meta'      => [
                                'title'     => "菜单管理",
                                'icon'      => "ant-design:menu-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/post/index",
                            'name'      => "system:post",
                            'component' => "/system/post/index",
                            'meta'      => [
                                'title'     => "岗位管理",
                                'icon'      => "ant-design:share-alt-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/dept/index",
                            'name'      => "system:dept",
                            'component' => "/system/dept/index",
                            'meta'      => [
                                'title'     => "部门管理",
                                'icon'      => "ant-design:deployment-unit-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/dict/index",
                            'name'      => "system:dict",
                            'component' => "/system/dict/index",
                            'meta'      => [
                                'title'     => "数据字典",
                                'icon'      => "ant-design:database-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                        [
                            'path'      => "/system/config/index",
                            'name'      => "system:config",
                            'component' => "/system/config/index",
                            'meta'      => [
                                'title'     => "参数配置",
                                'icon'      => "ant-design:file-text-outlined",
                                'keepAlive' => true,
                                'authList'  => [
                                    [
                                        'title'    => "超级管理员",
                                        'authMark' => "admin",
                                    ],
                                ],
                            ],
                            'children'  => [],
                        ],
                    ],
                ],
            ];

            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }
}
