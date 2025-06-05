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

use app\common\dao\system\SystemUserDao;
use app\common\model\system\SystemDataSource;
use app\common\model\system\SystemTenant;
use app\common\model\system\SystemUser;
use madong\basic\BaseService;
use madong\exception\AdminException;
use madong\exception\AuthException;
use madong\services\cache\CacheService;
use madong\utils\JwtAuth;
use support\Container;
use Webman\Event\Event;

class SystemUserService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUserDao::class);
    }

    /**
     * save
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
     *
     * @return void
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
     * @throws \Exception
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
     * @param string     $username
     * @param string     $password
     * @param string     $type
     * @param string     $grantType
     * @param string|int $tenantId
     *
     * @return array
     * @throws \Exception
     */
    public function login(string $username, string $password = '', string $type = 'admin', string $grantType = 'default', string|int $tenantId = ''): array
    {
        $tenantInfo = [];
        $map1       = [
            'user_name' => $username,
        ];
        if (config('app.tenant_enabled', false)) {
            if (empty($tenantId)) {
                throw new AuthException('Forbidden: Invalid tenant_id');
            }
            //验证数据源ID并设置数据源后续操作使用新的数据源
            $result = (new SystemTenant())->withoutGlobalScopes()->where('tenant_id', $tenantId)->first();
            if (empty($result)) {
                throw new AuthException('Forbidden: Invalid tenant_id');
            }
            $tenantInfo         = $result->toArray();
            $map1['tenant_id']  = $tenantId;
            request()->tenantId = $tenantId;
        }
        $adminInfo = $this->dao->getModel()->withoutGlobalScopes()->where($map1)->first();//注意get在dao被重写了使用model 直接获取
        $status    = 1;
        $message   = '登录成功';
        if (!$adminInfo) {
            $status  = 0;
            $message = '账号未注册，请重新输入!';
            throw new AdminException($message);
        }
        if ($adminInfo->status === 2) {
            $status  = 0;
            $message = '您已被禁止登录!';
        }
        $noPassword = ['sms', 'refresh_token'];//特殊第三方登录不验证密码
        if (!in_array($grantType, $noPassword) && !password_verify($password, $adminInfo->password)) {
            $status  = 0;
            $message = '密码错误，请重新输入!';
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
        $token = $jwt->createToken($adminInfo->id, $adminInfo->user_name, $type, $tenantInfo);
        // 登录事件
        Event::emit('user.login', compact('username', 'status', 'message', 'token', 'type'));

        return [
            'token'         => $token['token'],
            'user_id'       => $adminInfo->id,
            'client_id'     => $token['params']['jti']['client_id'] ?? '',//多租户或者账套客户端吗每次登录都会变
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function updateAvatarUser(string|int $id, string $avatar): mixed
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
     * @throws \Throwable
     */
    public function updateUserPwd(string|int $id, array $data): void
    {
        $this->transaction(function () use ($id, $data) {
            $info = $this->dao->getModel()->where('id', $id)->first();
            if (empty($info)) {
                throw new AdminException('用户不存在或被删除');
            }
            if (!password_verify($data['old_password'], $info->password)) {
                throw new AdminException('旧密码错误，请重新输入!');
            }
            return $this->dao->update(['id' => $id], ['password' => $this->passwordHash($data['new_password'])]);
        });
    }

    /**
     * 强制下线
     *
     * @param $data
     *
     * @throws \Throwable
     */
    public function kickoutByTokenValueUser($token): void
    {
        $this->transaction(function () use ($token) {
            $systemLoginLogService = Container::make(SystemLoginLogService::class);
            $cacheService          = Container::make(CacheService::class);
            $query                 = $systemLoginLogService->getModel()->query();
            $query->where('key', $token);
            $model             = $query->first();
            $model->expires_at = time() - 1;
            $model->remark     = '强制下线';
            $model->save();
            $cacheService->delete($token);
        });
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
        // 检测协议头存在性
        if (!preg_match('#^https?://#i', $url)) {
            return $url; // 非http(s)协议直接原样返回
        }
        // 支持子域名和端口号
        $pattern = '#^https?:// 
        (?:[^/@]+@)?              // 忽略用户认证信息 
        (?:[^/:]+|\[[^\]]+\])     // 匹配域名或IPv6地址 
        (?::\d+)?                 // 忽略端口号 
        (/[^\?#]*)                // 精确捕获路径部分 
        #x';
        preg_match($pattern, $url, $matches);
        return $matches[1] ?? '';
    }
}
