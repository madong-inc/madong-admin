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

use app\common\dao\system\SysAdminDao;
use app\common\model\system\SysAdmin;
use core\abstract\BaseService;
use core\cache\CacheService;
use core\casbin\Permission;
use core\enum\system\PolicyPrefix;
use core\exception\handler\AdminException;
use core\jwt\JwtToken;
use core\utils\RSAService;
use phpseclib3\Crypt\RSA;
use support\Container;
use Webman\Event\Event;

/**
 * @method getAdminInfo(string $username)
 * @method getAdminById($uid)
 * @method getAdminByName(string $username)
 */
class SysAdminService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SysAdminDao::class);
    }

    /**
     * save
     *
     * @param array $data
     *
     * @return SysAdmin|null
     * @throws \core\exception\handler\AdminException
     */
    public function save(array $data): SysAdmin|null
    {
        try {
            return $this->transaction(function () use ($data) {
                //1.0 添加用户数据
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $roles            = $data['role_id_list'] ?? [];
                $posts            = $data['post_id_list'] ?? [];
                $depts            = array_filter(explode(',', $data['dept_id'] ?? ''));
                unset($data['role_id_list'], $data['post_id_list'], $data['dept_id']);
                $model = $this->dao->save($data);

                $this->updateModel($model, $data, $depts, $posts, $roles);
                $this->syncRoles($model, $roles);
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
     * @return \app\common\model\system\SysAdmin|null
     * @throws \core\exception\handler\AdminException
     */
    public function update(int $id, array $data): ?SysAdmin
    {
        try {
            return $this->transaction(function () use ($id, $data) {
                // 更新用户基础数据
                $this->updatePasswordIfNeeded($data);
                $roles = $data['role_id_list'] ?? [];
                $posts = $data['post_id_list'] ?? [];
                $depts = array_filter(explode(',', $data['dept_id'] ?? ''));
                unset($data['role_id_list'], $data['post_id_list'], $data['dept_id']);
                $model = $this->dao->getModel()
                    ->findOrFail($id);
                $this->updateModel($model, $data, $depts, $posts, $roles);
                $this->syncRoles($model, $roles);
                return $model;
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 密码处理
     *
     * @param array $data
     */
    private function updatePasswordIfNeeded(array &$data): void
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
    }

    /**
     * 更新-部门职位关系
     *
     * @param \app\common\model\system\SysAdmin $model
     * @param array                             $data
     * @param array                             $depts
     * @param array                             $posts
     * @param array                             $roles
     */
    private function updateModel(SysAdmin $model, array $data, array $depts, array $posts, array $roles): void
    {
        $model->fill($data);
        $model->save();
        $model->depts()->sync($depts);
        $model->posts()->sync($posts);
        $model->roles()->sync($roles);
    }

    /**
     * 同步-角色casbin
     *
     * @param \app\common\model\system\SysAdmin $model
     * @param array                             $roles
     */
    private function syncRoles(SysAdmin $model, array $roles): void
    {
        // 格式化用户标识符
        $userIdentifier = 'user:' . strval($model->id);

        $domain = '*';

        // 获取当前用户在 Casbin 中的角色（带前缀）
        // Permission::getRolesForUser 返回的是带前缀的角色代码数组，如 ['role:admin', 'role:editor']
        $currentRoleCodes = Permission::getRolesForUser($userIdentifier, $domain);

        // 获取传入角色的详细信息
        $roleService = new SysRoleService();
        $rolesData   = $roleService->getAllRoles([
            'id' => $roles,
        ]);

        // 将传入角色的代码转换为数组，并添加 'role:' 前缀
        $incomingRoleCodes = array_map(
            function ($role) {
                return 'role:' . $role;
            },
            array_column($rolesData, 'id')
        );

        // 找出需要删除的角色（当前角色中不在传入角色列表中的角色）
        $rolesToDelete = array_diff($currentRoleCodes, $incomingRoleCodes);

        // 找出需要添加的角色（传入角色中不在当前角色列表中的角色）
        $rolesToAdd = array_diff($incomingRoleCodes, $currentRoleCodes);

        // 批量删除角色
        foreach ($rolesToDelete as $role) {
            Permission::deleteRoleForUser($userIdentifier, $role, $domain);
        }

        // 批量添加角色
        foreach ($rolesToAdd as $role) {
            Permission::addRoleForUser($userIdentifier, $role, $domain);
        }

        // 同步 Casbin 权限关联
        $casbinUserIdentifier = PolicyPrefix::USER->value . strval($model->id); // 根据实际情况调整
        $model->casbin()->sync([$casbinUserIdentifier]);
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
     * @param string $username
     * @param string $password
     * @param string $type
     * @param string $grantType
     * @param array  $params
     *
     * @return array
     * @throws \core\exception\handler\AdminException
     */
    public function login(string $username, string $password = '', string $type = 'admin', string $grantType = 'default', array $params = []): array
    {
        $adminInfo = $this->getAdminByName($username);
        $this->validateAdminStatus($adminInfo);
        $decryptedPassword = $this->validateRsaKeys($params['key_id'], $password);
        $this->validatePassword($adminInfo, $decryptedPassword, $grantType);
        [$userInfo, $token] = $this->generateTokenData($adminInfo, $type);
        $this->emitLoginSuccessEvent(array_merge($userInfo, $token), $tenant?->id ?? null);
        return $token ?? [];
    }

    /**
     * 验证管理员状态
     *
     * @param \app\common\model\system\SysAdmin|null $adminInfo
     *
     * @throws \core\exception\handler\AdminException
     */
    private function validateAdminStatus(?SysAdmin $adminInfo): void
    {
        if (!$adminInfo) {
            throw new AdminException('账号或密码错误，请重新输入!');
        }
        if ($adminInfo->enabled === 0) {
            throw new AdminException('您已被禁止登录!');
        }
        if ($adminInfo->is_locked === 1) {
            throw new AdminException('您的账号已被锁定，禁止登录!');
        }
    }

    /**
     * 验证密码
     *
     * @param \app\common\model\system\SysAdmin $adminInfo
     * @param string                            $password
     * @param string                            $grantType
     *
     * @throws \core\exception\handler\AdminException
     */
    private function validatePassword(SysAdmin $adminInfo, string $password, string $grantType): void
    {
        if (!in_array($grantType, ['sms', 'refresh_token']) && !password_verify($password, $adminInfo->password)) {
            $msg = '账号或密码错误，请重新输入!';
            $this->emitLoginFailedEvent($adminInfo->user_name, $msg);
            throw new AdminException($msg);
        }
    }

    /**
     * token生成
     *
     * @param \app\common\model\system\SysAdmin $adminInfo
     * @param string                            $type
     *
     * @return array
     */
    private function generateTokenData(SysAdmin $adminInfo, string $type): array
    {
        $adminInfo->login_time = time();
        $adminInfo->login_ip   = request()->getRealIp();
        $adminInfo->save();
        $userInfo              = $adminInfo->makeHidden([
            'password',
            'backend_setting',
            'created_by',
            'updated_by',
            'created_at',
            'deleted_at',
            'remark',
            'created_date',
            'updated_date',
        ])->toArray();
        $token                 = JwtToken::generateToken($userInfo);
        $token['client_id']    = $this->generateUniqueId();
        $token['expires_time'] = time() + $token['expires_in'];
        return [$userInfo, $token];
    }

    /**
     * 登录成功-事件发起
     *
     * @param array    $tokenData
     * @param int|null $tenantId
     */
    private function emitLoginSuccessEvent(array $tokenData, ?int $tenantId = null): void
    {
        $tokenData['tenant_id'] = $tenantId;
        $tokenData['status']    = 1;
        Event::emit('user.login', $tokenData);
    }

    /**
     * 登录失败-事件发起
     *
     * @param string $username
     * @param string $message
     * @param int    $status
     */
    private function emitLoginFailedEvent(string $username, string $message, int $status = 0): void
    {
        Event::emit('user.login', compact('username', 'status', 'message'));
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
     * 更新个人偏好设置
     *
     * @throws \Throwable
     */
    public function updateUserPreferences(string|int $id, array $data = []): void
    {
        $this->transaction(function () use ($id, $data) {
            unset($data['id']);
            return $this->dao->update(['id' => $id], ['backend_setting' => $data]);
        });
    }

    /**
     * 强制下线
     *
     * @param $token
     *
     * @throws \Throwable
     */
    public function kickoutByTokenValueUser($token): void
    {
        $this->transaction(function () use ($token) {
            $systemLoginLogService = Container::make(SysLoginLogService::class);
            // 使用 firstOrFail 避免空值检查
            $loginLog = $systemLoginLogService->getModel()
                ->where('key', $token)
                ->firstOrFail();

            // 批量更新字段
            $loginLog->update([
                'expires_at' => time(), // 使用 Carbon 时间
                'remark'     => '强制下线',
                'updated_at' => time(), // 确保更新时间戳
            ]);

            // 添加Token 致黑名单
            $result = JwtToken::addToBlacklist($token, true);
            if (!$result) {
                throw new AdminException('操作失败');
            }
        });
    }

    /**
     * 生成唯一UUID
     *
     * @param string|null $currentIp
     *
     * @return string
     */
    private function generateUniqueId(string|null $currentIp = null): string
    {
        if (empty($currentIp)) {
            $currentIp = request()->getRemoteIp();
        }
        return uniqid($currentIp . '-', true);
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

    /**
     * 校验密钥
     *
     * @param $keyId
     * @param $encryptedPassword
     *
     * @return string
     * @throws AdminException
     */
    private function validateRsaKeys($keyId, $encryptedPassword): string
    {
        $cache      = Container::make(CacheService::class, []);
        $privateKey = $cache->get("rsa_private_key_$keyId");
        if (!$privateKey) {
            throw new AdminException('私钥不存在或已过期，请刷新页面重试');
        }
        return RSAService::decrypt($encryptedPassword, $privateKey);
    }
}


