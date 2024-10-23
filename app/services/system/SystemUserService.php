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
use madong\utils\JwtAuth;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;
use support\Request;
use think\facade\Db;
use Webman\Event\Event;

/**
 * @method save(array $data)
 */
class SystemUserService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUserDao::class);
    }

    /**
     * 用户详情
     *
     * @param $id
     *
     * @return array
     */
    public function read($id): array
    {
        $result                  = $this->dao->get($id, ['*'], ['roles', 'posts', 'depts'])->hidden(['password'])->toArray();
        $result['selected_role'] = array_column($result['roles'] ?? [], 'id');
        $result['selected_post'] = array_column($result['posts'] ?? [], 'id');
        return $result;
    }

    /**
     * 用户登录
     *
     * @param string $username
     * @param string $password
     * @param string $type
     *
     * @return array
     */
    public function login(string $username, string $password, string $type): array
    {
        $adminInfo = $this->dao->get(['user_name' => $username]);
        $status    = 1;
        $message   = '登录成功';
        if (!$adminInfo) {
            $status  = 0;
            $message = '账号或密码错误，请重新输入!';
            throw new AdminException($message);
        }
        if ($adminInfo->status === 2) {
            $status  = 0;
            $message = '您已被禁止登录!';
        }
        if (!password_verify($password, $adminInfo->password)) {
            $status  = 0;
            $message = '账号或密码错误，请重新输入!';
        }
        if ($status === 0) {
            // 登录事件
            Event::emit('user.login', compact('username', 'status', 'message'));
            throw new AdminException($message);
        }
        $adminInfo->login_time = time();
        $adminInfo->login_ip   = request()->getRealIp();
        $adminInfo->save();

        $jwt   = new JwtAuth();
        $token = $jwt->createToken($adminInfo->id, $adminInfo->user_name, $type);
        // 登录事件
        Event::emit('user.login', compact('username', 'status', 'message'));

        return [
            'token'        => $token['token'],
            'expires_time' => $token['params']['exp'],
        ];
    }

    /**
     * 获取用户列表-角色id
     *
     * @param array $where
     *
     * @return array
     */
    public function getUsersListByRoleId(array $where): array
    {
        [$page, $limit, $defaultLimit, $limitMax] = $this->getPageValue();
        $roleId = $where['role_id'];
        //1.0 获取总数
        $total = $this->dao->getModel()->hasWhere('userRoles', function ($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->when(!empty($where), function ($query) use ($where) {
            foreach ($where as $key => $value) {
                if (in_array($key, ['user_name', 'real_name']) && $value !== null && $value !== '') {
                    $query->where($key, 'like', $value . '%');
                }
            }
        })->count();

        //2.0 获取列表
        $items = $this->dao->getModel()->hasWhere('userRoles', function ($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->when(!empty($where), function ($query) use ($where) {
            foreach ($where as $key => $value) {
                if (in_array($key, ['user_name', 'real_name']) && $value !== null && $value !== '') {
                    $query->where($key, 'like', $value . '%');
                }
            }
        })->select()->toArray();

        return compact('total', 'items');
    }

    /**
     * 添加用户
     *
     * @param $data
     *
     * @return mixed
     */
    public function add($data): mixed
    {
        Db::startTrans();
        try {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $roles            = $data['selected_role'] ?? [];
            $posts            = $data['selected_post'] ?? [];
            $model            = $this->save($data);
            if (!empty($roles)) {
                $model->roles()->saveAll($roles);
            }
            if (!empty($posts)) {
                $model->posts()->saveAll($roles);
            }
            Db::commit();
            return [$model->getPk() => $model->getKey()];
        } catch (\Throwable $e) {
            Db::rollback();
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 编辑
     *
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function edit($id, $data): mixed
    {
        Db::startTrans();
        try {
            unset($data['password']);
            $roles  = $data['selected_role'] ?? [];
            $posts  = $data['selected_post'] ?? [];
            $result = $this->dao->update(['id' => $id], $data);
            $user   = $this->dao->get($id);
            if ($result && $user) {
                $user->roles()->detach();
                $user->posts()->detach();
                $user->roles()->saveAll($roles);
                if (!empty($posts)) {
                    $user->posts()->save($posts);
                }
            }
            Db::commit();
            return [$result->getPk() => $result->getKey()];
        } catch (\Throwable $e) {
            Db::rollback();
            throw new AdminException($e->getMessage());
        }

    }

    /**
     * 删除用户
     *
     * @param array|string $data
     */
    public function batchDelete(array|string $data): void
    {
        try {
            if (is_string($data)) {
                $data = array_map('trim', explode(',', $data));
            }
            $ret = $this->dao->count([['id', 'in', $data], ['is_super', '=', 1]]);

            if ($ret > 0) {
                throw new AdminException('系统内置用户，不允许删除');
            }
            $this->dao->destroy($data);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 用户冻结
     *
     * @param array|string $id
     * @param string|int   $status
     */
    public function lockMultiple(array|string $id, string|int $status)
    {
        try {
            if (is_string($id)) {
                $id = array_map('trim', explode(',', $id));
            }
            $ret = $this->dao->count([['id', 'in', $id], ['is_super', '=', 1]]);
            if ($ret > 0 && $status == 2) {
                throw new AdminException('系统内置用户，不允许冻结');
            }
            $this->dao->batchUpdate($id, ['status' => $status]);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
