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
use app\admin\validate\system\SysAdminValidate;
use app\common\services\system\SysAdminRoleService;
use app\common\services\system\SysAdminService;
use app\common\services\system\SysLoginLogService;
use core\exception\handler\AdminException;
use core\utils\Json;
use support\Container;
use support\Request;

class SysAdminController extends Crud
{

    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SysAdminService::class);
        $this->validate = Container::make(SysAdminValidate::class);
    }

    public function index(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            [$total, $list] = $this->service->getList($where, $field, $page, $limit, $order, [], false);
            return call_user_func([$this, $format_function], $list, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
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
            $data = $this->inputFilter($request->all(), ['post_id_list', 'role_id_list', 'dept_id']);
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
            $data = $this->inputFilter($request->all(), ['post_id_list', 'role_id_list', 'dept_id']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('update')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($data['id'], $data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 获取管理员详情
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function show(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id');
            /** @var  SysAdminService $service */
            $service = $this->service;
            $result  = $service->getAdminById($id)->toArray();
            return Json::success('ok', $result);
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
            $this->service->batchUpdate(['id' => $ids], $data);
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
            $systemUserRoleService = Container::make(SysAdminRoleService::class);
            $systemUserRoleService->save($data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function updateInfo(Request $request): \support\Response
    {
        try {
            $uid  = getCurrentUser();
            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                $data['id'] = $uid;
                if (!$this->validate->scene('update-info')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->updateUserInfo($uid, $data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新头像
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function updateAvatar(Request $request): \support\Response
    {
        try {
            $uid  = getCurrentUser();
            $data = $request->input('avatar');
            $this->service->updateAvatarUser($uid, $data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新个人密码
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function updatePwd(Request $request): \support\Response
    {
        try {
            $uid  = getCurrentUser();
            $data = $this->inputFilter($request->all(), ['confirm_password', 'new_password', 'old_password']);
            if (isset($this->validate) && $this->validate) {
                $data['id'] = $uid;
                if (!$this->validate->scene('update-pwd')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->updateUserPwd($uid, $data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新用户前端偏好设置
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function updatePreferences(Request $request): \support\Response
    {
        try {
            $uid  = getCurrentUser();
            $data = $request->all();
            if (isset($this->validate) && $this->validate) {
                $data['id'] = $uid;
                if (!$this->validate->scene('update-preferences')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->updateUserPreferences($uid, $data);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 我的在线用户
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function onlineDevice(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $adminInfo             = getCurrentUser(true);
            $where                 = [
                ['user_name', '=', $adminInfo['user_name']],
                ['expires_at', '>', time()],
            ];
            $systemLoginLogService = Container::make(SysLoginLogService::class);
            $total                 = $systemLoginLogService->getCount($where);
            $items                 = $systemLoginLogService->selectList($where, $field, $page, $limit, 'login_time desc', [], false);
            return Json::success('ok', compact('total', 'items'));
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 强制下线
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function kickoutByTokenValue(Request $request): \support\Response
    {
        try {
            $token = $request->route->param('id', null);
            $this->service->kickoutByTokenValueUser($token);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
