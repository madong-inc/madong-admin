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

namespace app\adminapi\controller\message;

use app\adminapi\controller\Crud;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\schema\request\message\MessageFormRequest;
use app\adminapi\schema\request\message\MessageQueryRequest;
use app\adminapi\schema\response\system\MessageResponse;
use app\adminapi\validate\system\MessageValidate;
use app\enum\system\MessageStatus;
use app\schema\request\BatchDeleteRequest;
use app\schema\request\IdRequest;
use app\service\admin\message\MessageService;
use core\tool\Json;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\AllowAnonymous;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\RequestBody;
use support\Request;
use support\annotation\Middleware;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class MessageController extends Crud
{
    public function __construct(MessageService $service, MessageValidate $validate)
    {
        $this->service  = $service;
        $this->validate = $validate;
    }

    #[OA\Get(
        path: '/message',
        summary: '列表',
        tags: ['消息管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => MessageQueryRequest::class,
        ]
    )]
    #[Permission(code: 'network:message:list')]
    #[PageResponse(MessageResponse::class)]
    public function index(Request $request): \support\Response
    {
        $uid                 = getCurrentUser();
        $data                = $request->get();
        $data['receiver_id'] = $uid;//消息接收人
        $request->setGet($data);
        return parent::index($request);
    }

    #[OA\Get(
        path: '/message/{id}',
        summary: '详情',
        tags: ['消息管理'],
        x: [
            SchemaConstants::X_PROPERTY_IN => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission(code: 'network:message:read')]
    #[SimpleResponse(schema: MessageResponse::class, example: [])]
    public function show(Request $request): \support\Response
    {
        return parent::show($request);
    }

    #[OA\Post(
        path: '/message',
        summary: '创建',
        tags: ['消息管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => MessageFormRequest::class,
        ]
    )]
    #[Permission(code: 'network:message:create')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function store(Request $request): \support\Response
    {
        return parent::store($request);
    }

    #[OA\Put(
        path: '/message/{id}',
        summary: '更新',
        tags: ['消息管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => MessageFormRequest::class,
        ]
    )]
    #[OA\Parameter(
        name: 'id',
        description: '消息ID（雪花ID）',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            example: '123456789012345678'
        )
    )]
    #[Permission(code: 'network:message:update')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function update(Request $request): \support\Response
    {
        return parent::update($request);
    }

    #[OA\Delete(
        path: '/message/{id}',
        summary: '删除',
        tags: ['消息管理'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: '消息ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    default: 0,
                    example: '123456789012345678',
                )
            ),
        ],
        x: [
            SchemaConstants::X_PROPERTY_IN => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission(code: 'network:message:delete')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function destroy(Request $request): \support\Response
    {
        return parent::destroy($request);
    }

    #[OA\Delete(
        path: '/message',
        summary: '批量删除',
        tags: ['消息管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => BatchDeleteRequest::class,
        ]
    )]
    #[Permission(code: 'network:message:delete')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function batchDelete(Request $request): \support\Response
    {
        return parent::destroy($request);
    }

    #[OA\Put(
        path: '/message/{id}/update-read',
        summary: '消息状态标记',
        tags: ['消息管理'],
    )]
    #[OA\Parameter(
        name: 'id',
        description: '消息ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            default: 0,
            example: '123456789012345678',
        )
    )]
    #[RequestBody(required: true, content: new OA\JsonContent(
        required: ['status'],
        properties: [
            new OA\Property(
                property: 'status',
                description: '消息状态（0=未读，1=已读）',
                type: 'integer',
                enum: [0, 1],
                example: 1
            ),
        ]
    ))]
    #[Permission(code: 'network:message:update_read')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function updateRead(Request $request): \support\Response
    {
        try {
            $data = $request->all();
            if ($this->validate) {
                $this->validate->scene('update-read')->check($data);
            }
            $actionMap = [
                MessageStatus::READ->value   => 'isRead',
                MessageStatus::UNREAD->value => 'isUnread',
            ];

            if (!isset($actionMap[$data['status']])) {
                throw new \InvalidArgumentException('Invalid message status');
            }

            $this->service->{$actionMap[$data['status']]}(
                $data['id'] ?? throw new \InvalidArgumentException('Missing message ID')
            );
            return Json::success('ok');
        } catch (\Throwable $e) {
            // 统一异常处理（生产环境建议记录日志）
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Post(
        path: '/message/notify-on-first-login-to-all',
        summary: '推送广播&消息',
        tags: ['消息管理'],
    )]
    #[Permission(code: 'network:message:push')]
    #[AllowAnonymous(requireToken: true,requirePermission: false)]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function notifyOnFirstLoginToAll(Request $request): \support\Response
    {
        try {
            $id = getCurrentUser();
            $this->service->notifyOnFirstLoginToAll($id);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }
}
