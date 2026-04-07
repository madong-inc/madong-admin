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

namespace app\adminapi\controller\logs;

use app\adminapi\controller\Crud;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\schema\request\logs\LoginLogQueryRequest;
use app\adminapi\schema\response\system\LoginLogResponse;
use app\schema\request\BatchDeleteRequest;
use app\schema\request\IdRequest;
use app\service\admin\logs\LoginLogService;
use core\exception\handler\AdminException;
use core\tool\Json;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use support\Request;
use support\annotation\Middleware;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class LoginLogController extends Crud
{
    public function __construct(LoginLogService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: '/logs/login',
        summary: '列表',
        tags: ['日志管理.登录'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => LoginLogQueryRequest::class,
        ]
    )]
    #[Permission(code: 'logs:login:list')]
    #[PageResponse(schema: LoginLogResponse::class, example: [])]
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
            $total           = $this->service->getCount($where);
            $list            = $this->service->selectList($where, $field, $page, $limit, $order, ['account' => function ($query) {
                $query->select(['id', 'user_name', 'real_name']);
            }], false);
            return call_user_func([$this, $format_function], $list, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/logs/login/{id}',
        summary: '详情',
        tags: ['日志管理.登录'],
    )]
    #[OA\Parameter(
        name: 'id',
        description: '日志ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: 1)
    )]
    #[Permission(code: 'logs:login:read')]
    #[SimpleResponse(schema: LoginLogResponse::class, example: [])]
    public function show(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id');
            $data = $this->service->get($id,['*'],['account' => function ($query) {
                $query->select(['id', 'user_name', 'real_name']);
            }]);
            if (empty($data)) {
                throw new AdminException('数据未找到');
            }
            return Json::success('ok', $data->toArray());
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    #[OA\Delete(
        path: '/logs/login/{id}',
        summary: '删除',
        tags: ['日志管理.登录'],
        x: [
            SchemaConstants::X_PROPERTY_IN    => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission(code: 'logs:login:delete')]
    #[SimpleResponse(schema: [], example: [])]
    public function destroy(Request $request): \support\Response
    {
        return parent::destroy($request);
    }

    #[OA\Delete(
        path: '/logs/login',
        summary: '批量删除',
        tags: ['日志管理.登录'],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => BatchDeleteRequest::class,
        ]
    )]
    #[Permission(code: 'logs:login:delete')]
    #[SimpleResponse(schema: [], example: [])]
    public function batchDelete(Request $request): \support\Response
    {
        return parent::destroy($request);
    }
}
