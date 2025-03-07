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

use app\model\system\SystemUser;
use app\model\system\SystemUserPost;
use madong\helper\PropertyCopier;
use madong\services\cache\CacheService;
use support\Container;
use Webman\Event\Event;
use madong\utils\JwtAuth;
use madong\basic\BaseService;
use madong\exception\AdminException;
use app\dao\system\SystemUserDao;

class SystemUserService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUserDao::class);
    }

    /**
     * 新增
     *
     * @param array $data
     *
     * @return SystemUser|null
     */
    public function save(array $data): SystemUser|null
    {
        try {
            return $this->transaction(function () use ($data) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $roles            = $data['role_id_list'] ?? [];
                $posts            = $data['post_id_list'] ?? [];
                $model            = $this->dao->save($data);
                $model->roles()->sync($roles);
                $model->posts()->sync($posts);
                return $model;
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 编辑
     *
     * @param int   $id
     * @param array $data
     */
    public function update(int $id, array $data): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                // 处理角色和职位
                $roles = $data['role_id_list'] ?? [];
                $posts = $data['post_id_list'] ?? [];
                // 处理密码
                unset($data['password']);
                // 更新用户信息
                $user = $this->dao->getModel()->where('id', $id)->first();
                $user->update($data);
                $user->roles()->sync($roles);
                $user->posts()->sync($posts);
            });
        } catch (\Throwable $e) {
            // 记录日志或添加更多上下文信息到异常中
            throw new AdminException("Failed to update user: {$e->getMessage()}");
        }
    }

    /**
     * destroy
     *
     * @param $id
     * @param $force
     *
     * @return mixed
     */
    public function destroy($id, $force): mixed
    {
        $ret = $this->dao->count([['id', 'in', $id], ['is_super', '=', 1]]);
        if ($ret > 0) {
            throw new AdminException('系统内置用户，不允许删除');
        }
        return $this->dao->destroy($id);
    }

    /**
     * 用户-冻结
     *
     * @param array|string $id
     */
    public function locked(array|string $id): void
    {
        try {
            if (is_string($id)) {
                $id = array_map('trim', explode(',', $id));
            }
            $ret = $this->dao->count([['id', 'in', $id], ['is_super', '=', 1]]);
            if ($ret > 0) {
                throw new AdminException('系统内置用户，不允许冻结');
            }
            $this->dao->batchUpdate($id, ['is_locked' => 1]);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 用户-解除冻结
     *
     * @param array|string $id
     */
    public function unLocked(array|string $id): void
    {
        try {
            if (is_string($id)) {
                $id = array_map('trim', explode(',', $id));
            }
            $this->dao->batchUpdate($id, ['is_locked' => 0]);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 用户登录
     *
     * @param string $username
     * @param string $password
     * @param string $type
     * @param string $grantType
     *
     * @return array
     */
    public function login(string $username, string $password = '', string $type = 'admin', string $grantType = 'default'): array
    {
        $adminInfo = $this->dao->getModel()->where('user_name',$username)->first();//注意get在dao被重写了使用mode 直接获取
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
        $noPassword = ['sms', 'refresh_token'];//特殊第三方登录不验证密码
        if (!in_array($grantType, $noPassword) && !password_verify($password, $adminInfo->password)) {
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
        Event::emit('user.login', compact('username', 'status', 'message', 'token', 'type'));

        return [
            'token'         => $token['token'],
            'user_id'       => $adminInfo->id,
            'refresh_token' => '',
            'expires_time'  => $token['params']['exp'],
        ];
    }

    /**
     * 更新用户信息
     *
     * @param string|int $id
     * @param array      $data
     *
     * @return mixed
     */
    public function updateUserInfo(string|int $id, array $data): mixed
    {
        return $this->dao->update(['id' => $id], $data);
    }

    /**
     * 更新用户头像
     *
     * @param string|int $id
     * @param string     $avatar
     *
     * @return mixed
     */
    public function updateAvatarUser(string|int $id, string $avatar)
    {
        return $this->dao->update(['id' => $id], ['avatar' => $this->getPathFromUrl($avatar)]);
    }

    /**
     * 更新个人用户密码
     *
     * @param string|int $id
     * @param array      $data
     *
     * @return void
     */
    public function updateUserPwd(string|int $id, array $data): void
    {
        $this->transaction(function () use ($id, $data) {
            $info = $this->dao->getModel()->where('id', $id)->first();
            if (empty($info)) {
                throw new AdminException('用户不存在或被删除');
            }
            if (!password_verify($data['password'], $info->password)) {
                throw new AdminException('旧密码错误，请重新输入!');
            }
            return $this->dao->update(['id' => $id], ['password' => $this->passwordHash($data['new_password'])]);
        });
    }

    /**
     * 强制下线
     *
     * @param $data
     */
    public function kickoutByTokenValueUser($data): void
    {
        $this->transaction(function () use ($data) {
            $systemLoginLogService = Container::make(SystemLoginLogService::class);
            $cacheService          = Container::make(CacheService::class);
            $systemLoginLogService->update([['key', 'in', $data]], ['expires_time' => time() - 1, 'remark' => '强制下线']);
            foreach ($data as $key => $value) {
                $cacheService->delete($value);
            }
        });
    }

    /**
     * 用户授权角色
     *
     * @param string|int $id
     * @param array      $data
     */
    public function userRoleGrant(string|int $id, array $data = []): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                $model = $this->dao->getModel()->where('id', $id)->first();
                $model->roles()->sync($data);
            });
        } catch (\Throwable $e) {
            // 记录日志或添加更多上下文信息到异常中
            throw new AdminException("Failed to update user: {$e->getMessage()}");
        }
    }

    /**
     * 入参移除url
     *
     * @param string $url
     *
     * @return string
     */
    private function getPathFromUrl(string $url): string
    {
        // 如果没有协议，添加 http:// 作为默认
        if (!preg_match('#^https?://#', $url)) {
            $url = 'http://' . $url; // 或者根据需要选择 https
        }
        preg_match('#^https?://[^/]+(/.*)$#', $url, $matches);
        return isset($matches[1]) ? ltrim($matches[1], '/') : '';
    }
}
