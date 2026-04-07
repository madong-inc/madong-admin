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

namespace app\adminapi\controller\profile;

use app\adminapi\controller\Crud;
use app\adminapi\CurrentUser;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\schema\request\profile\ProfileUpdateRequest;
use app\adminapi\schema\request\profile\PasswordUpdateRequest;
use app\adminapi\schema\request\profile\AvatarUpdateRequest;
use app\adminapi\validate\profile\ProfileValidate;
use app\dao\logs\LoginLogDao;
use app\schema\request\IdRequest;
use app\service\admin\logs\LoginLogService;
use app\service\admin\system\AdminService;
use core\tool\Json;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\AllowAnonymous;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use support\annotation\Middleware;
use support\Container;
use support\Request;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[OA\Tag(name: '个人中心')]
#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class ProfileController extends Crud
{
    public function __construct(AdminService $service, ProfileValidate $validate)
    {
        $this->service  = $service;
        $this->validate = $validate;
    }

    #[OA\Get(
        path: '/profile',
        summary: '获取当前用户信息',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['个人中心']
    )]
    #[SimpleResponse(example: '{"id": 1, "user_name": "admin", "email": "test@example.com"}')]
    #[Permission(code: 'admin:profile:info')]
    #[AllowAnonymous(requireToken: true, requirePermission: false)]
    public function show(Request $request): \support\Response
    {
        try {
            $userId = getCurrentUser();
            $data   = $this->service->get($userId);
            return Json::success('ok', $data);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/profile',
        summary: '更新个人信息',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['个人中心']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: ProfileUpdateRequest::class)
    )]
    #[SimpleResponse(example: '{"code": 0, "msg": "success"}')]
    #[Permission(code: 'admin:profile:update_info')]
    #[AllowAnonymous(requireToken: true, requirePermission: false)]
    public function update(Request $request): \support\Response
    {
        try {
            $userId = getCurrentUser();
            $data   = $this->inputFilter($request->all());

            // 验证数据
            if (!$this->validate->scene('update-profile')->check($data)) {
                throw new \Exception($this->validate->getError());
            }

            $this->service->updateUserInfo($userId, $data);
            return Json::success('个人信息更新成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/profile/password',
        summary: '修改密码',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['个人中心']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: PasswordUpdateRequest::class)
    )]
    #[SimpleResponse(example: '{"code": 0, "msg": "密码修改成功"}')]
    #[Permission(code: 'admin:profile:password')]
    #[AllowAnonymous(requireToken: true, requirePermission: false)]
    public function updatePassword(Request $request): \support\Response
    {
        try {
            $userId = getCurrentUser();
            $data   = $this->inputFilter($request->all(), ['old_password', 'new_password', 'confirm_password']);

            // 验证数据
            if (!$this->validate->scene('update-password')->check($data)) {
                throw new \Exception($this->validate->getError());
            }

            $this->service->updateUserPwd($userId, $data);
            return Json::success('密码修改成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/profile/avatar',
        summary: '更新头像',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['个人中心']
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[Permission(code: 'admin:profile:avatar')]
    #[AllowAnonymous(requireToken: true, requirePermission: false)]
    public function updateAvatar(Request $request): \support\Response
    {
        try {
            $userId = getCurrentUser();
            $avatar = $request->input('avatar');

            // 验证数据
            if (!$this->validate->scene('update-avatar')->check(['avatar' => $avatar])) {
                throw new \Exception($this->validate->getError());
            }

            $this->service->updateAvatarUser($userId, $avatar);
            return Json::success('头像更新成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/profile/sessions',
        summary: '获取在线设备列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['个人中心']
    )]
    #[OA\Parameter(
        name: 'page',
        description: '页码',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    #[OA\Parameter(
        name: 'limit',
        description: '每页数量',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 10)
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[Permission(code: 'admin:profile:sessions')]
    #[AllowAnonymous(requireToken: true, requirePermission: false)]
    public function getSessions(Request $request): \support\Response
    {
        try {
            $page  = (int) $request->input('page', 1);
            $limit = (int) $request->input('limit', 10);
            
            $currUser = Container::make(CurrentUser::class);
            $jwt = new \core\jwt\JwtToken();
            
            // 获取当前用户信息（用于兜底）
            $adminInfo = $currUser->admin(true) ?? [];
            $defaultUserName = $adminInfo['user_name'] ?? '';
            $defaultRealName = $adminInfo['real_name'] ?? '';
            $defaultAvatar = $adminInfo['avatar'] ?? '';
            
            // 从 JWT 获取真实的会话列表
            $allSessions = $jwt->getSessions($currUser->id());
            $total = count($allSessions);
            
            // 分页处理
            $offset = ($page - 1) * $limit;
            $sessions = array_slice($allSessions, $offset, $limit);
            
            // 格式化数据
            $items = array_map(function ($session) use ($defaultUserName, $defaultRealName, $defaultAvatar) {
                $extra = $session['extra'] ?? [];
                return [
                    'jti' => $session['jti'],
                    'client_type' => $session['client_type'] ?? 'admin',
                    'login_time' => $session['created_at'] ?? time(),
                    'ip' => $extra['ip'] ?? '',
                    'ip_location'=>$extra['ip_location'] ?? '',
                    'os' => $extra['os'] ?? '',
                    'browser' => $extra['browser'] ?? '',
                    'user_name' => $extra['user_name'] ?? $defaultUserName,
                    'real_name' => $extra['real_name'] ?? $defaultRealName,
                    'avatar' => $extra['avatar'] ?? $defaultAvatar,
                ];
            }, $sessions);
            
            return Json::success('ok', compact('total', 'items'));
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * @throws \Throwable
     */
    #[OA\Delete(
        path: '/profile/sessions/{jti}',
        summary: '强制下线指定设备',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['个人中心']
    )]
    #[OA\Parameter(
        name: 'jti',
        description: '会话JTI',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[Permission(code: 'admin:profile:kickout')]
    #[AllowAnonymous(requireToken: true, requirePermission: false)]
    public function kickoutSession(Request $request): \support\Response
    {
        try {
            $jti = $request->route->param('jti', null);
            $jwt = new \core\jwt\JwtToken();
            $result = $jwt->kickoutByJti($jti);
            
            if (!$result) {
                return Json::fail('会话不存在或已失效');
            }
            
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/profile/{id}/update-preferences',
        summary: '更新用户前端偏好设置',
        tags: ['用户管理'],
        x: [
            SchemaConstants::X_PROPERTY_IN    => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'preferences',
                    description: '偏好设置',
                    type: 'string',
                    example: '{"theme": "dark"}'
                ),
            ]
        )
    )]
    #[Permission(code: 'system:admin:update_preferences')]
    #[SimpleResponse(schema: [], example: [])]
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
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }
}
