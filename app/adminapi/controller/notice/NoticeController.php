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

namespace app\adminapi\controller\notice;

use app\adminapi\controller\Crud;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\schema\request\notice\NoticeFormRequest;
use app\adminapi\schema\request\notice\NoticeQueryRequest;
use app\adminapi\schema\response\system\NoticeResponse;
use app\adminapi\validate\system\NoticeValidate;
use app\schema\request\BatchDeleteRequest;
use app\schema\request\IdRequest;
use app\service\admin\notice\NoticeService;
use core\tool\Json;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use support\Request;
use support\annotation\Middleware;
use Webman\RedisQueue\Client;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class NoticeController extends Crud
{
    // 注入服务和验证器
    public function __construct(NoticeService $service, NoticeValidate $validate)
    {
        $this->service  = $service;
        $this->validate = $validate;
    }

    #[OA\Get(
        path: '/notice',
        summary: '列表',
        tags: ['公告管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => NoticeQueryRequest::class,
        ]
    )]
    #[Permission(code: 'network:notice:list')]
    #[PageResponse(schema:NoticeResponse::class,example: [])]
    public function index(Request $request): \support\Response
    {
        return parent::index($request); // 调用父类Crud的列表方法
    }

    #[OA\Get(
        path: '/notice/{id}',
        summary: '详情',
        tags: ['公告管理'],
        x: [
            SchemaConstants::X_PROPERTY_IN => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission(code: 'network:notice:read')]
    #[SimpleResponse(schema: NoticeResponse::class, example: ['code' => 0, 'message' => 'success', 'data' => []])] // 详情响应
    public function show(Request $request): \support\Response
    {
        return parent::show($request); // 调用父类Crud的详情方法
    }

    #[OA\Post(
        path: '/notice',
        summary: '创建',
        tags: ['公告管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => NoticeFormRequest::class,
        ]
    )]
    #[Permission(code: 'network:notice:create')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function store(Request $request): \support\Response
    {
        return parent::store($request);
    }

    // 4. 更新公告
    #[OA\Put(
        path: '/notice/{id}',
        summary: '更新',
        tags: ['公告管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => NoticeFormRequest::class,
        ]
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: '123456789012345678')
    )]
    #[Permission(code: 'network:notice:update')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])] // 更新响应
    public function update(Request $request): \support\Response
    {
        return parent::update($request); // 调用父类Crud的更新方法
    }

    #[OA\Delete(
        path: '/notice/{id}',
        summary: '删除',
        tags: ['公告管理'],
        x: [
            SchemaConstants::X_PROPERTY_IN => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission(code: 'network:notice:delete')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])] // 删除响应
    public function destroy(Request $request): \support\Response
    {
        return parent::destroy($request); // 调用父类Crud的删除方法
    }

    #[OA\Delete(
        path: '/notice',
        summary: '批量删除',
        tags: ['公告管理'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => BatchDeleteRequest::class,
        ]
    )]
    #[Permission(code: 'network:notice:delete')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function batchDelete(Request $request): \support\Response
    {
        return parent::destroy($request);
    }

    #[OA\Post(
        path: '/notice/publish',
        summary: '发布',
        tags: ['公告管理'],
    )]
    #[Permission(code: 'network:notice:publish')]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    public function publish(Request $request): \support\Response
    {
        try {
            $id    = $request->input('id', null);
            $model = $this->service->get($id);
            $queue = 'admin-announcement-push';
            Client::send($queue, $model->makeVisible('tenant_id')->toArray(), 0);
            return Json::success('ok', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }
}
