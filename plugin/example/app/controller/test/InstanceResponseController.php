<?php

declare(strict_types=1);

namespace plugin\example\app\controller\test;


use madong\swagger\annotation\response\DataResponse;
use madong\swagger\annotation\response\ListResponse;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use plugin\example\app\schema\response\AdminSchema;
use plugin\example\app\schema\response\TestResponseDTO;
use support\Request;
use support\Response;

class InstanceResponseController
{
    /**
     * DataResponse - 对象实例模式
     */
    #[OA\Get(
        path: '/test/instance-data',
        description: '使用 DataResponse 注解并传入对象实例',
        summary: 'DataResponse - 对象实例模式',
        tags: ['Instance响应测试']
    )]
    #[DataResponse(
        schema: new TestResponseDTO(),
        description: '成功获取测试DTO数据'
    )]
    #[Permission(code: 'test:schema:data', operation: 'OR', description: 'DataResponse 实例模式测试权限')]
    public function testDataInstance(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'id'         => '1',
                'username'   => 'testuser1',
                'email'      => 'test1@example.com',
                'status'     => 1,
                'created_at' => '2024-01-01T00:00:00+08:00',
            ],
        ]);
    }

    /**
     * PageResponse - 对象实例模式
     */
    #[OA\Get(
        path: '/test/instance-page',
        description: '使用 PageResponse 注解并传入对象实例',
        summary: 'PageResponse - 对象实例模式',
        tags: ['Instance响应测试']
    )]
    #[PageResponse(
        schema: new AdminSchema(),
        description: '成功获取管理员分页列表',
        totalExample: 100,
        pageExample: 1,
        limitExample: 10
    )]
    #[Permission(code: 'test:schema:page', operation: 'OR', description: 'PageResponse 实例模式测试权限')]
    public function testPageInstance(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'total' => 100,
                'page'  => 1,
                'limit' => 10,
                'items' => [
                    [
                        'id'          => '1',
                        'user_name'   => 'admin1',
                        'real_name'   => '管理员1',
                        'mobile_phone'=> '18888888881',
                        'email'       => 'admin1@example.com',
                        'status'      => 1,
                        'dept_id'     => 1,
                    ],
                ],
            ],
        ]);
    }

    /**
     * ListResponse - 对象实例模式
     */
    #[OA\Get(
        path: '/test/instance-list',
        description: '使用 ListResponse 注解并传入对象实例',
        summary: 'ListResponse - 对象实例模式',
        tags: ['Instance响应测试']
    )]
    #[ListResponse(
        schema: new AdminSchema(),
        description: '成功获取管理员列表'
    )]
    #[Permission(code: 'test:schema:list', operation: 'OR', description: 'ListResponse 实例模式测试权限')]
    public function testListInstance(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                [
                    'id'          => '1',
                    'user_name'   => 'admin1',
                    'real_name'   => '管理员1',
                    'email'       => 'admin1@example.com',
                ],
                [
                    'id'          => '2',
                    'user_name'   => 'admin2',
                    'real_name'   => '管理员2',
                    'email'       => 'admin2@example.com',
                ],
            ],
        ]);
    }

    /**
     * 混合使用：对象实例 + 示例
     */
    #[OA\Get(
        path: '/test/instance-with-example',
        description: '使用对象实例定义Schema，同时提供自定义示例',
        summary: '混合使用：对象实例 + 示例',
        tags: ['Instance响应测试']
    )]
    #[PageResponse(
        schema: new AdminSchema(),
        example: [
            [
                'id'          => '999',
                'user_name'   => 'custom_admin',
                'real_name'   => '自定义管理员',
                'mobile_phone'=> '13999999999',
                'email'       => 'custom@example.com',
                'status'      => 1,
                'dept_id'     => 99,
            ]
        ],
        description: '成功获取分页列表（带自定义示例）',
        totalExample: 50,
        pageExample: 2,
        limitExample: 20
    )]
    #[Permission(code: 'test:schema:mixed', operation: 'OR', description: '混合模式测试权限')]
    public function testMixed(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'total' => 50,
                'page'  => 2,
                'limit' => 20,
                'items' => [
                    [
                        'id'          => '21',
                        'user_name'   => 'admin21',
                        'real_name'   => '管理员21',
                        'email'       => 'admin21@example.com',
                    ],
                ],
            ],
        ]);
    }
}
