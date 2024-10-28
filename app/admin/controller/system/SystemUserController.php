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

    /**
     * 插入
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function store(Request $request): \support\Response
    {

        try {
            $data = $this->inputFilter($request->all(), ['post_id_list', 'role_id_list']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $model = $this->service->save($data);
            if (empty($model)) {
                throw new AdminException('插入失败');
            }
            $pk = $model->getPk();
            return Json::success('ok', [$pk => $model->getData($pk)]);
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
            $id   = $request->route->param('id');
            $data = $this->inputFilter($request->all(), ['post_id_list', 'role_id_list']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('update')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($id, $data);
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
    public function locked(Request $request): \support\Response
    {
        try {
            // 获取单个 ID 和多个 ID
            $data = $request->input('data', []);
            $id   = $request->input('id');

            // 如果提供了多个 ID，优先使用多个 ID
            if (!empty($data)) {
                $id = $data;
            } else if (empty($id)) {
                return Json::fail('Either id or data must be provided.');
            }
            $this->service->locked($id);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 用户-解除冻结
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function unLocked(Request $request): \support\Response
    {
        try {
            // 获取单个 ID 和多个 ID
            $data = $request->input('data', []);
            $id   = $request->input('id');

            // 如果提供了多个 ID，优先使用多个 ID
            if (!empty($data)) {
                $id = $data;
            } else if (empty($id)) {
                return Json::fail('Either id or data must be provided.');
            }
            $this->service->unLocked($id);
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

    /**
     * 用户-授权角色
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function grantRole(Request $request): \support\Response
    {
        try {
            $data                  = $this->inputFilter($request->all(), ['user_id', 'role_id_list']);
            $systemUserRoleService = Container::make(SystemUserRoleService::class);
            $systemUserRoleService->save($data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
