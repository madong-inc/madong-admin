<?php
declare(strict_types=1);
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

namespace app\service\admin\system;

use app\adminapi\event\LoginLogEvent;
use app\dao\system\AdminDao;
use app\enum\system\PolicyPrefix;
use app\model\system\Admin;
use app\scope\global\AccessPermissionScope;
use app\service\admin\logs\LoginLogService;
use core\base\BaseService;
use core\cache\CacheService;
use core\exception\handler\AdminException;
use core\jwt\JwtToken;
use core\tool\RSAService;
use support\Container;
use Webman\Event\Event;

/**
 * @method getAdminInfo(string $username)
 * @method getAdminById($uid, $withoutScopes = null)
 * @method getList(mixed $where, mixed $field, mixed $page, mixed $limit, mixed $order, array $array, false $false)
 * @method getUsersListByRoleId(mixed $where, mixed $field, mixed $page, mixed $limit)
 * @method getAdminByName(string $username, array|null $withoutScopes)
 */
class AdminService extends BaseService
{

    public function __construct(AdminDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * save
     *
     * @param array $data
     *
     * @return Admin|null
     * @throws \core\exception\handler\AdminException
     */
    public function save(array $data): Admin|null
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
     * @param int|string $id
     * @param array      $data
     *
     * @return \app\model\system\Admin|null
     * @throws \core\exception\handler\AdminException
     */
    public function update(int|string $id, array $data): ?Admin
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
     * @param \app\model\system\Admin $model
     * @param array                   $data
     * @param array                   $depts
     * @param array                   $posts
     * @param array                   $roles
     */
    private function updateModel(Admin $model, array $data, array $depts, array $posts, array $roles): void
    {
        $model->fill($data);
        $model->save();
        $model->depts()->sync($depts);
        $model->posts()->sync($posts);
        $model->roles()->sync($roles);
    }

    /**
     * 同步-角色
     *
     * @param \app\model\system\Admin $model
     * @param array                   $roles
     */
    private function syncRoles(Admin $model, array $roles): void
    {
        // 只需要更新角色关联，权限缓存会在 CurrentUser 中使用时自动处理
        // 使用 Eloquent 的 sync 方法会自动更新中间表
        $model->roles()->sync($roles);
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
        $adminInfo = $this->getAdminByName($username, [AccessPermissionScope::class]);
        $this->validateAdminStatus($adminInfo);
        $decryptedPassword = $this->validateRsaKeys($params['key_id'], $password);
//        $decryptedPassword= $password;//临时注释关闭RSA验证要不无法调试
        $this->validatePassword($adminInfo, $decryptedPassword, $grantType);
        [$userInfo, $token] = $this->generateTokenData($adminInfo, $type);
        $this->emitLoginSuccessEvent(array_merge($userInfo, $token), $tenant?->id ?? null);
        return $token ?? [];
    }

    /**
     * 第三方应用登录
     *
     * @param mixed  $acctId
     * @param mixed  $appId
     * @param mixed  $appSecret
     * @param mixed  $userName
     * @param string $type
     *
     * @return array
     * @throws \core\exception\handler\AdminException
     */
    public function thirdPartyLogin(mixed $acctId, mixed $appId, mixed $appSecret, mixed $userName, string $type = 'admin'): array
    {
        $adminInfo = $this->getAdminByName($userName);
        $this->validateAdminStatus($adminInfo);
        $this->validateThirdPartyApp($acctId, $appId, $appSecret);
        [$userInfo, $token] = $this->generateTokenData($adminInfo, $type);
        $this->emitLoginSuccessEvent(array_merge($userInfo, $token), $tenant?->id ?? null);
        return $token ?? [];
    }

    private function validateThirdPartyApp(mixed $acctId, mixed $appId, mixed $appSecret)
    {

        if ($acctId !== config('app.acct_id')) {
            throw new AdminException('第三方应用不存在或已删除');
        }
        if ($appId !== config('app.app_id')) {
            throw new AdminException('第三方应用ID错误');
        }
        if ($appSecret !== config('app.app_secret')) {
            throw new AdminException('第三方应用ID或密钥错误');
        }
    }

    /**
     * 验证管理员状态
     *
     * @param \app\model\system\Admin|null $adminInfo
     *
     * @throws \core\exception\handler\AdminException
     */
    private function validateAdminStatus(?Admin $adminInfo): void
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
     * @param \app\model\system\Admin $adminInfo
     * @param string                  $password
     * @param string                  $grantType
     *
     * @throws \core\exception\handler\AdminException
     */
    private function validatePassword(Admin $adminInfo, string $password, string $grantType): void
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
     * @param \app\model\system\Admin $adminInfo
     * @param string                  $type
     *
     * @return array
     */
    private function generateTokenData(Admin $adminInfo, string $type): array
    {
        $loginIp = request()->getRealIp();
        $userAgent = request()->header('user-agent', '');
        $browser = $this->getBrowser($userAgent);
        $os = $this->getOs($userAgent);
        $ipLocation = $this->getIpLocation($loginIp);
        
        $adminInfo->login_time = time();
        $adminInfo->login_ip   = $loginIp;
        $adminInfo->save();
        $userInfo = $adminInfo->makeHidden([
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
        
        // 追加扩展数据到 userInfo（用于 JWT extra）
        $userInfo['ip'] = $loginIp;
        $userInfo['ip_location'] = $ipLocation;
        $userInfo['browser'] = $browser;
        $userInfo['os'] = $os;
        
        // 使用新的 JwtToken 生成 token
        $jwt = new JwtToken();
        $tokenObj = $jwt->generate((string)$adminInfo->id, $type, $userInfo);
        
        $token = [
            'access_token' => $tokenObj->accessToken,
            'refresh_token' => $tokenObj->refreshToken,
            'expires_in' => $tokenObj->expiresIn,
            'client_id' => $this->generateUniqueId($loginIp),
            'expires_time' => time() + $tokenObj->expiresIn
        ];
        
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
        $loginIp = request()->getRealIp();
        $event = new LoginLogEvent(
            '登录成功',
            request()->app,
            $loginIp,
            $this->getIpLocation($loginIp),
            $this->getBrowser(request()->header('user-agent', '')),
            $this->getOs(request()->header('user-agent', '')),
            0,
            '登录成功',
            $tokenData['user_name'],
            $tokenData['id'],
            time(),
            $tokenData['access_token'],
            $tokenData['expires_time']
        );
        $event->dispatch();
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
        $loginIp = request()->getRealIp();
        $event = new LoginLogEvent(
            '登录失败',
            request()->app,
            $loginIp,
            $this->getIpLocation($loginIp),
            $this->getBrowser(request()->header('user-agent', '')),
            $this->getOs(request()->header('user-agent', '')),
            -1,
            $message,
            $username,
            0,
            time(),
            '',
            time()
        );
        $event->dispatch();
    }
    
    /**
     * 获取浏览器信息
     *
     * @param string $userAgent
     * @return string
     */
    private function getBrowser(string $userAgent): string
    {
        $br = 'Unknown';
        if (preg_match('/MSIE/i', $userAgent)) {
            $br = 'MSIE';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $br = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $br = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $br = 'Safari';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    }
    
    /**
     * 获取操作系统信息
     *
     * @param string $userAgent
     * @return string
     */
    private function getOs(string $userAgent): string
    {
        $os = 'Unknown';
        if (preg_match('/win/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/mac/i', $userAgent)) {
            $os = 'Mac';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } else {
            $os = 'Other';
        }
        return $os;
    }
    
    /**
     * 获取IP归属地
     *
     * @param string $ip
     * @return string
     */
    private function getIpLocation(string $ip): string
    {
        // 本地IP或无效IP直接返回
        if (empty($ip) || in_array($ip, ['127.0.0.1', '::1', 'localhost', '0.0.0.0'])) {
            return '本地';
        }
        
        // 使用新浪API获取IP归属地
        try {
            $response = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=" . $ip);
            if ($response === false) {
                return '未知';
            }
            $data = json_decode($response, true);
            if (isset($data['ret']) && $data['ret'] === 1 && !empty($data['city'])) {
                $location = $data['city'];
                if (!empty($data['province']) && strpos($data['province'], $data['city']) === false) {
                    $location = $data['province'] . ' ' . $location;
                }
                return $location;
            }
        } catch (\Throwable $e) {
            // 忽略异常
        }
        
        return '未知';
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
            /** @var LoginLogService $systemLoginLogService */
            $systemLoginLogService = Container::make(LoginLogService::class);
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
     * 删除关联源-并清理残留关联数据
     *
     * @param array $ids
     *
     * @return array
     * @throws \core\exception\handler\AdminException
     */
    public function batchDelete(array $ids): array
    {
        try {
            return $this->transaction(function () use ($ids) {
                // 1. 验证：禁止删除超级管理员
                $superUserCount = $this->dao->getModel()->whereIn('id', $ids)
                    ->where('is_super', 1)
                    ->count();
                if ($superUserCount > 0) {
                    throw new AdminException('系统内置用户不允许删除');
                }

                // 2. 关联同步：通过模型关联清理角色中间表数据

                $admins = $this->dao->getModel()->whereIn('id', $ids)->get();
                foreach ($admins as $admin) {
                    // 解除角色关联
                    $admin->roles()->detach();
                    $admin->depts()->detach();
                }

                // 4. 执行管理员数据删除
                $deleteCount = $this->dao->destroy($ids);
                if ($deleteCount <= 0) {
                    throw new AdminException('删除失败，未找到有效用户');
                }
                return ['id' => $ids];
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
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


