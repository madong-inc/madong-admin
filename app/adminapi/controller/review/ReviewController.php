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

namespace app\adminapi\controller\review;

use app\adminapi\controller\Crud;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\schema\request\IdRequest;
use app\service\admin\review\ReviewService;
use madong\swagger\annotation\response\DataResponse;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\Permission;
use core\tool\Json;
use OpenApi\Attributes as OA;
use support\annotation\Middleware;
use support\Request;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[OA\Tag(name: '审核管理', description: '通用审核模块')]
#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class ReviewController extends Crud
{

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: '/review',
        summary: '审核列表',
        tags: ['审核管理'],
        parameters: [
            new OA\Parameter(name: "status", description: "审核状态：0待审核，1已通过，2已拒绝", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "reviewable_type", description: "审核类型（morph_map别名）", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "page", description: "页码", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "limit", description: "每页数量", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "LIKE_title", description: "标题模糊搜索", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "LIKE_applicant", description: "申请人模糊搜索", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "LIKE_content", description: "内容模糊搜索", in: "query", schema: new OA\Schema(type: "string")),
        ],
    )]
    #[Permission(code: "review:list")]
    #[PageResponse(schema: [], example: [[
                                             'id'               => '230104551592014400',
                                             'reviewable_type'  => 'question',
                                             'reviewable_id'    => '230104551591014400',
                                             'status'           => 0,
                                             'reason'           => '同意',
                                             'reviewer_id'      => 1,
                                             'reviewed_at'      => 1758899600,
                                             'flow_instance_id' => null,
                                             'extra_data'       => null,
                                             'created_by'       => 1,
                                             'updated_by'       => 1,
                                             'created_at'       => '2026-03-02T09:12:13.000000Z',
                                             'updated_at'       => '2026-03-02T09:08:38.000000Z',
                                             'title'            => '请问Madong框架可以免费商用吗？',
                                             'content'          => '我想用到项目上可以吗！阿打发斯蒂芬阿斯顿发生大法师',
                                             'applicant'        => '未知',
                                             'reviewer_name'    => '超级管理员',
                                             'display_name'     => 'question',
                                             'morph_alias'      => null,
                                             'status_text'      => '待审核',
                                             'is_workflow_mode' => false,
                                             'reviewer'         => [
                                                 'id'           => '1',
                                                 'user_name'    => 'superAdmin',
                                                 'real_name'    => '超级管理员',
                                                 'nick_name'    => '超级管理员',
                                                 'created_date' => null,
                                                 'updated_date' => null,
                                             ],
                                             'reviewable'       => [
                                                 'id'               => '230104551591014400',
                                                 'category_id'      => '2',
                                                 'title'            => '请问Madong框架可以免费商用吗？',
                                                 'content'          => '我想用到项目上可以吗！阿打发斯蒂芬阿斯顿发生大法师',
                                                 'deleted_at'       => null,
                                                 'created_at'       => '2025-09-26T15:13:20.000000Z',
                                                 'updated_at'       => '2026-03-03T06:39:01.000000Z',
                                                 'view_count'       => 3869,
                                                 'like_count'       => 0,
                                                 'collect_count'    => 0,
                                                 'comment_count'    => 0,
                                                 'member_id'        => '277105476800872448',
                                                 'is_sticky'        => 0,
                                                 'sticky_order'     => 0,
                                                 'is_excellent'     => 0,
                                                 'is_solved'        => 0,
                                                 'solved_answer_id' => null,
                                                 'created_date'     => '2025-09-26 23:13:20',
                                                 'updated_date'     => '2026-03-03 14:39:01',
                                             ],
                                         ]])]
    public function index(Request $request): \support\Response
    {
        try {

            // 使用 selectInput 处理参数（现在 title/applicant/content 字段已被跳过）
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);

            // 准备关联预加载
            $with = [
                'reviewer' => function ($query) {
                    $query->select(['id', 'user_name', 'real_name', 'nick_name']);
                },
            ];

            // 获取映射后的审核列表（支持 extra_data 字段搜索）
            $items = $this->service->getMappedList($where, $field, $page, $limit, $order, $with, false);

            // 使用支持搜索的计数方法
            $total = $this->service->getCountWithSearch($where);
            return $this->formatNormal($items, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/review/{id}',
        summary: '审核详情',
        tags: ['审核管理'],
        x: [
            SchemaConstants::X_PROPERTY_IN    => 'id',
            SchemaConstants::X_SCHEMA_REQUEST => IdRequest::class,
        ]
    )]
    #[Permission("review:read")]
    #[DataResponse(schema: [], example: [
        'id'               => '230104551592014400',
        'reviewable_type'  => 'question',
        'reviewable_id'    => '230104551591014400',
        'status'           => 0,
        'reason'           => '同意',
        'reviewer_id'      => 1,
        'reviewed_at'      => 1758899600,
        'flow_instance_id' => null,
        'extra_data'       => null,
        'created_by'       => 1,
        'updated_by'       => 1,
        'created_at'       => '2026-03-02T09:12:13.000000Z',
        'updated_at'       => '2026-03-02T09:08:38.000000Z',
        'title'            => '请问Madong框架可以免费商用吗？',
        'content'          => '我想用到项目上可以吗！阿打发斯蒂芬阿斯顿发生大法师',
        'applicant'        => '未知',
        'reviewer_name'    => '超级管理员',
        'display_name'     => 'question',
        'morph_alias'      => null,
        'status_text'      => '待审核',
        'is_workflow_mode' => false,
        'reviewer'         => [],
        'reviewable'       => [],
    ])]
    public function show(Request $request): \support\Response
    {
        try {
            $id     = $request->route->param('id');
            $detail = $this->service->getMappedReview($id);
            if (!$detail) {
                return Json::fail('审核记录不存在');
            }
            return Json::success('获取成功', $detail);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/review/{id}/approve',
        summary: '审核通过',
        tags: ['审核管理'],
        parameters: [
            new OA\Parameter(name: "id", description: "审核ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
    )]
    #[Permission("review:approve")]
    #[SimpleResponse(schema: [], example: [])]
    public function approve(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id');
            $this->service->approve($id);
            return Json::success('审核通过');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/review/{id}/reject',
        summary: '审核拒绝',
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "reason", type: "string", example: "内容违规"),
            ])
        ),
        tags: ['审核管理'],
        parameters: [
            new OA\Parameter(name: "id", description: "审核ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
    )]
    #[Permission("review:reject")]
    #[SimpleResponse(schema: [], example: [])]
    public function reject(Request $request): \support\Response
    {
        try {
            $id     = $request->route->param('id');
            $reason = $request->input('reason', '');
            $this->service->reject($id, $reason);
            return Json::success('审核拒绝');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Post(
        path: '/review/batch/approve',
        summary: '批量审核通过',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "ids", type: "array", items: new OA\Items(type: "integer"), example: [1, 2, 3]),
            ])
        ),
        tags: ['审核管理'],
    )]
    #[Permission("review:approve")]
    #[SimpleResponse(schema: [], example: [])]
    public function batchApprove(Request $request): \support\Response
    {
        try {
            $ids = $request->input('ids', []);
            $reason = $request->input('reason', '');
            if (empty($ids)) {
                return Json::fail('请选择要审核的记录');
            }
            $count = $this->service->batchApprove($ids, ['reason' => $reason]);
            return Json::success('批量审核通过成功', ['count' => $count]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Post(
        path: '/review/batch/reject',
        summary: '批量审核拒绝',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "ids", type: "array", items: new OA\Items(type: "integer"), example: [1, 2, 3]),
                new OA\Property(property: "reason", type: "string", example: "内容违规"),
            ])
        ),
        tags: ['审核管理'],
    )]
    #[Permission("review:reject")]
    #[SimpleResponse(schema: [],example: [])]
    public function batchReject(Request $request): \support\Response
    {
        try {
            $ids = $request->input('ids', []);
            $reason = $request->input('reason', '');
            if (empty($ids)) {
                return Json::fail('请选择要审核的记录');
            }
            $count = $this->service->batchReject($ids, $reason, ['reason' => $reason]);
            return Json::success('批量审核拒绝成功', ['count' => $count]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/review/review/statistics',
        summary: '审核统计',
        tags: ['审核管理'],
    )]
    #[Permission("review:list")]
    #[SimpleResponse(schema: [],example: [])]
    public function statistics(Request $request): \support\Response
    {
        try {
            $result = $this->service->getStatistics();
            return Json::success('获取成功', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Delete(
        path: '/review/{id}',
        summary: '删除审核记录',
        tags: ['审核管理'],
        parameters: [
            new OA\Parameter(name: "id", description: "审核ID", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ]
    )]
    #[Permission("review:delete")]
    #[SimpleResponse(schema: [],example: [])]
    public function destroy(Request $request): \support\Response
    {
        return parent::destroy($request);
    }
}
