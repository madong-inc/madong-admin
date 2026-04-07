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

namespace app\adminapi\controller\member;

use app\adminapi\controller\Crud;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\validate\member\MemberTagValidate;
use app\schema\request\BatchDeleteRequest;
use app\schema\request\IdRequest;
use app\service\admin\member\MemberTagService;
use core\tool\Json;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use support\Request;
use support\annotation\Middleware;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class TagController extends Crud
{

    public function __construct(MemberTagService $service, MemberTagValidate $validate)
    {
        $this->service  = $service;
        $this->validate = $validate;
    }

    #[OA\Get(
        path: '/member/tag',
        summary: '列表',
        tags: ['会员标签'],
        parameters: [
            new OA\Parameter(name: "name", description: "标签名称", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "status", description: "状态", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "page", description: "页码", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "limit", description: "每页数量", in: "query", schema: new OA\Schema(type: "integer")),
        ]
    )]
    #[Permission("member:tag:list")]
    public function index(Request $request): \support\Response
    {
        return parent::index($request);
    }

    #[OA\Get(
        path: '/member/tag/{id}',
        summary: '详情',
        tags: ['会员标签'],
        responses: [
            new OA\Response(
                response: 200,
                description: "成功",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "code", type: "integer", example: 0),
                    new OA\Property(property: "msg", type: "string", example: "获取成功"),
                    new OA\Property(property: "data", properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "活跃会员"),
                        new OA\Property(property: "color", type: "string", example: "#409EFF"),
                        new OA\Property(property: "status", type: "integer", example: 1),
                        new OA\Property(property: "sort", type: "integer", example: 100),
                        new OA\Property(property: "create_time", type: "integer", example: 1640995200),
                    ], type: "object"),
                ])
            ),
        ],
        x: [
            SchemaConstants::X_PROPERTY_IN => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission("member:tag:read")]
    public function show(Request $request): \support\Response
    {
        return parent::show($request);
    }

    #[OA\Post(
        path: '/member/tag',
        summary: '新增',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "name", type: "string", example: "活跃会员"),
                new OA\Property(property: "color", type: "string", example: "#409EFF"),
                new OA\Property(property: "status", type: "integer", example: 1),
                new OA\Property(property: "sort", type: "integer", example: 100),
            ])
        ),
        tags: ['会员标签']
    )]
    #[Permission("member:tag:create")]
    public function store(Request $request): \support\Response
    {
        return parent::store($request);
    }

    #[OA\Put(
        path: '/member/tag/{id}',
        summary: '更新',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "name", type: "string", example: "活跃会员"),
                new OA\Property(property: "color", type: "string", example: "#409EFF"),
                new OA\Property(property: "status", type: "integer", example: 1),
                new OA\Property(property: "sort", type: "integer", example: 100),
            ])
        ),
        tags: ['会员标签'],
        parameters: [
            new OA\Parameter(name: "id", description: "标签ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "成功",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "code", type: "integer", example: 0),
                    new OA\Property(property: "msg", type: "string", example: "更新成功"),
                    new OA\Property(property: "data", properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                    ], type: "object"),
                ])
            ),
        ]
    )]
    #[Permission("member:tag:update")]
    public function update(Request $request): \support\Response
    {
        return parent::update($request);
    }

    #[OA\Delete(
        path: '/member/tag/{id}',
        summary: '删除',
        tags: ['会员标签'],
        parameters: [
            new OA\Parameter(name: "id", description: "标签ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        x: [
            SchemaConstants::X_PROPERTY_IN => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission("member:tag:delete")]
    public function destroy(Request $request): \support\Response
    {
        return parent::destroy($request);
    }

    #[OA\Delete(
        path: '/member/tag',
        summary: '批量删除',
        tags: ['会员标签'],
        x: [SchemaConstants::X_SCHEMA_REQUEST => BatchDeleteRequest::class],
    )]
    #[Permission("member:tag:delete")]
    public function batchDelete(Request $request): \support\Response
    {
        return parent::destroy($request);
    }

    #[OA\Put(
        path: '/member/{id}/permissions',
        summary: '为标签分配权限',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "permissions", type: "array", items: new OA\Items(type: "string"), example: ["developer", "api_access"]),
            ])
        ),
        tags: ['会员标签'],
        parameters: [
            new OA\Parameter(name: "id", description: "标签ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
    )]
    #[SimpleResponse(example: ['code' => 0, 'message' => 'success', 'data' => []])]
    #[Permission("member:tag:assign_permissions")]
    public function assignPermissions(Request $request, $id): \support\Response
    {
        try {
            $permissions = $request->input('permissions', []);
            $this->service->assignPermissions($id, $permissions);
            return Json::success('权限分配成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/member/{id}/permissions',
        summary: '获取标签权限',
        tags: ['会员标签'],
        parameters: [
            new OA\Parameter(name: "id", description: "标签ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
    )]
    #[Permission("member:tag:show_permissions")]
    public function getPermissions(Request $request, $id): \support\Response
    {
        try {
            $tag = $this->service->get($id, ['*'], ['permissions']);
            if (!$tag) {
                return Json::fail('标签不存在');
            }
            return Json::success('ok', $tag->permissions->toArray());
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/member/all-with-permissions',
        summary: '获取所有启用的标签（含权限）',
        tags: ['会员标签'],
    )]
    #[Permission("member:tag:all")]
    public function getAllWithPermissions(Request $request): \support\Response
    {
        try {
            $result = $this->service->getEnabledTagsWithPermissions();
            return Json::success('ok', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/member/available-permissions',
        summary: '获取所有可用的权限码（从菜单表）',
        tags: ['会员标签'],
    )]
    #[Permission("member:tag:all")]
    public function getAvailablePermissions(Request $request): \support\Response
    {
        try {
            $result = $this->service->getAvailablePermissions();
            return Json::success('ok', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
