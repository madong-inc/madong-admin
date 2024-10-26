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
use app\admin\validate\system\SystemUserValidate;
use app\services\system\SystemUserRoleService;
use app\services\system\SystemUserService;
use madong\exception\AdminException;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemUserController extends Crud
{

    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemUserService::class);
        $this->validate = Container::make(SystemUserValidate::class);
    }

    public function index(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $total = $this->service->count($where, true);
            $items = $this->service->selectList($where, $field, $page, $limit, '', ['depts', 'posts', 'roles'], true)->hidden(['password'])->toArray();
            return Json::success('ok', compact('total', 'items'));
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 下拉列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function select(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $items = $this->service->selectList($where, $field, $page, $limit, '', ['depts', 'posts', 'roles'], true)->hidden(['password'])->toArray();
            return Json::success('ok', $items);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function store(Request $request): \support\Response
    {

        try {
            $data = $request->all();
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $result = $this->service->add($data);
            if (empty($result)) {
                throw new AdminException('插入失败');
            }
            return Json::success('ok', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function update(Request $request): \support\Response
    {
        try {
            $id     = $request->route->param('id');
            $data   = $request->all();
            $result = $this->service->edit($id, $data);
            if (empty($result)) {
                throw new AdminException('更新失败');
            }
            return Json::success('ok', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 删除用户
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function destroy(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id'); // 获取路由地址 id
            $data = $request->input('data', []);
            $id   = !empty($id) && $id !== '0' ? $id : $data;
            if (empty($id)) {
                throw new AdminException('参数错误');
            }
            // 调用服务进行批量删除
            $this->service->batchDelete($id);
            return Json::success('操作成功');
        } catch (AdminException $e) {
            return Json::fail($e->getMessage());
        } catch (\Exception $e) {
            return Json::fail('操作失败: ' . $e->getMessage());
        }
    }

    /**
     * 详情
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function show(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id');
            $data = $this->service->read($id);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 用户列表-角色
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function userListByRoleId(Request $request): \support\Response
    {
        try {
            $where = $request->more([['username'], ['nickname'], ['role_id']]);
            if (empty($where['role_id'])) {
                throw new \Exception('缺少role_Id 参数');
            }
            $data = $this->service->getUsersListByRoleId($where);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 用户-关联角色
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function saveUserRole(Request $request): \support\Response
    {
        try {
            $roleId  = $request->input('role_id');
            $data    = $request->input('user_id', []);
            $service = Container::make(SystemUserRoleService::class);
            $service->usersToRoleById($roleId, $data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }

    /**
     * 移除用户-角色关联
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function removeUserRole(Request $request): \support\Response
    {
        try {
            $roleId  = $request->input('role_id');
            $userId  = $request->input('user_id');
            $service = Container::make(SystemUserRoleService::class);
            $service->removeUserRole($roleId, $userId);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }

    /**
     * 批量移除用户-角色关联
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function batchRemoveUserRole(Request $request): \support\Response
    {
        try {
            $roleId  = $request->input('role_id');
            $data    = $request->input('data');
            $service = Container::make(SystemUserRoleService::class);
            $service->removeUserRole($roleId, $data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 用户锁定-禁用/反禁用
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function lock(Request $request): \support\Response
    {
        try {
            // 获取单个 ID 和多个 ID
            $ids = $request->input('ids', []);
            $id  = $request->input('id');

            // 如果提供了多个 ID，优先使用多个 ID
            if (!empty($ids)) {
                $id = $ids;
            } elseif (empty($id)) {
                return Json::fail('Either id or ids must be provided.');
            }

            // 获取状态
            $status = $request->input('enabled');
            if (empty($status)) {
                return Json::fail('Status must be provided.');
            }
            // 锁定用户
            $this->service->lockMultiple($id, ['enabled' => $status]);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 重置密码
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function changePassword(Request $request): \support\Response
    {
        try {
            $ids      = $request->input('ids');
            $password = $request->input('password', 123456);
            $data     = ['password' => password_hash($password, PASSWORD_DEFAULT)];
            $this->service->update(['id' => $ids], $data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
