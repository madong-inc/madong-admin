<?php

declare(strict_types=1);

namespace plugin\example\app\controller\test;


use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use madong\swagger\annotation\response\DataResponse;
use madong\swagger\annotation\response\ListResponse;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\annotation\response\ResultResponse;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\annotation\response\TreeResponse;
use madong\swagger\attribute\AllowAnonymous;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use plugin\example\app\schema\response\AdminSchema;
use plugin\example\app\schema\response\DeptSchema;
use support\annotation\Middleware;
use support\Request;
use support\Response;

#[Middleware( OperationMiddleware::class,PermissionMiddleware::class)]
class ResponseTestController
{

    #[OA\Get(
        path: '/test/simple',
        summary: '简单响应测试（使用Schema类）',
        tags: ['Response测试']
    )]
    #[SimpleResponse(
        schema: AdminSchema::class,
        description: '操作成功',
        response: 200
    )]
    #[SimpleResponse(description: '参数错误', response: 400)]
    #[SimpleResponse(description: '未授权', response: 401)]
    #[SimpleResponse(description: '服务器错误', response: 500)]
    #[Permission(code:'test:simple',operation:'OR',description:'简单响应测试权限')]
    #[AllowAnonymous(requireToken: true,requirePermission: true,description:'简单响应测试跳过权限校验')]
    public function testSimple(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'id'          => 1,
                'username'    => 'admin',
                'email'       => 'admin@example.com',
                'status'      => 1,
                'create_time' => '2024-01-01 00:00:00',
            ],
        ]);
    }

    #[OA\Get(
        path: '/test/simple-array',
        summary: '简单响应测试（使用数组示例）',
        tags: ['Response测试']
    )]
    #[SimpleResponse(
        example: ['flag' => true, 'message' => '删除成功'],
        description: '操作成功'
    )]
    public function testSimpleArray(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => ['flag' => true],
        ]);
    }

    #[OA\Get(
        path: '/test/data',
        summary: '单个对象响应测试（使用Schema类）',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: AdminSchema::class,
        description: '获取管理员详情'
    )]
    #[DataResponse(
        schema: AdminSchema::class,
        description: '获取用户成功',
        response: 200
    )]
    #[DataResponse(
        example: ['error' => '用户不存在'],
        description: '用户未找到',
        response: 404
    )]
    #[DataResponse(
        example: ['error' => '无权访问'],
        description: '权限不足',
        response: 403
    )]
    public function testData(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'id'          => 1,
                'username'    => 'admin',
                'email'       => 'admin@example.com',
                'status'      => 1,
                'create_time' => '2024-01-01 00:00:00',
            ],
        ]);
    }



    #[OA\Get(
        path: '/test/page',
        summary: '分页响应测试',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: AdminSchema::class,
        example: ['id' => 1, 'username' => 'admin1', 'email' => 'admin1@example.com'],
        description: '获取管理员分页列表',
        totalExample: 100,
        pageExample: 1,
        limitExample: 10
    )]
    public function testPage(Request $request): Response
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
                        'id'          => 1,
                        'username'    => 'admin1',
                        'email'       => 'admin1@example.com',
                        'status'      => 1,
                        'create_time' => '2024-01-01 00:00:00',
                    ],
                    [
                        'id'          => 2,
                        'username'    => 'admin2',
                        'email'       => 'admin2@example.com',
                        'status'      => 1,
                        'create_time' => '2024-01-02 00:00:00',
                    ],
                ],
            ],
        ]);
    }

    #[OA\Get(
        path: '/test/result-boolean',
        summary: '布尔结果响应测试（返回true/false）',
        tags: ['Response测试']
    )]
    #[ResultResponse(
        example: true,
        description: '布尔结果响应，返回true或false（无需Schema）'
    )]
    public function testResultBoolean(Request $request): Response
    {
        $isAvailable = $request->get('available', true);

        return json([
            'code'    => 0,
            'message' => $isAvailable ? 'success' : '用户名已存在',
            'data'    => $isAvailable,
        ]);
    }

    #[OA\Get(
        path: '/test/tree',
        summary: '树形结构响应测试（使用Schema类）',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        schema: DeptSchema::class,
        example: [
            [
                'id'       => 1,
                'name'     => '总部',
                'children' => [
                    ['id' => 2, 'name' => '技术部', 'children' => []],
                    ['id' => 3, 'name' => '市场部', 'children' => []],
                ],
            ],
        ],
        description: '获取部门树（DeptSchema）'
    )]
    public function testTree(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                [
                    'id'       => 1,
                    'name'     => '总部',
                    'children' => [
                        [
                            'id'       => 2,
                            'name'     => '技术部',
                            'children' => [
                                ['id' => 4, 'name' => '前端组', 'children' => []],
                                ['id' => 5, 'name' => '后端组', 'children' => []],
                            ],
                        ],
                        ['id' => 3, 'name' => '市场部', 'children' => []],
                    ],
                ],
            ],
        ]);
    }

    #[OA\Get(
        path: '/test/combined',
        summary: '复杂对象响应测试（Schema+详细示例）',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: AdminSchema::class,
        example: [
            'id'          => 999,
            'username'    => 'combined_test_user',
            'email'       => 'combined@example.com',
            'status'      => 1,
            'create_time' => '2024-03-11 12:00:00',
        ],
        description: '返回复杂对象数据（Schema+详细示例）'
    )]
    public function testCombined(Request $request): Response
    {
        return json([
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'id'          => 1,
                'username'    => 'updated_admin',
                'email'       => 'updated@example.com',
                'status'      => 1,
                'create_time' => '2024-01-01 00:00:00',
            ],
        ]);
    }

    // ==================== DataResponse 多种场景 ====================

    /**
     * DataResponse - Class 模式（推荐）
     */
    #[OA\Get(
        path: '/test/data-class',
        summary: 'DataResponse - Class 模式（使用Schema类）',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: AdminSchema::class,
        description: '获取管理员详情 - Class 模式'
    )]
    public function testDataClass(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'mobile_phone' => '18888888888',
                'email' => 'admin@example.com',
                'status' => 1,
                'dept_id' => 1,
            ],
        ]);
    }

    /**
     * DataResponse - JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/data-json',
        summary: 'DataResponse - JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[DataResponse(
        example: '{"id": 1,"user_name": "admin","real_name": "管理员","mobile_phone": "18888888888","email": "admin@example.com","status": 1,"dept_id": 1}',
        description: '获取管理员详情 - JSON 模式'
    )]
    public function testDataJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'mobile_phone' => '18888888888',
                'email' => 'admin@example.com',
                'status' => 1,
                'dept_id' => 1,
            ],
        ]);
    }

    /**
     * DataResponse - 数组对象模式
     */
    #[OA\Get(
        path: '/test/data-array',
        summary: 'DataResponse - 数组对象模式',
        tags: ['Response测试']
    )]
    #[DataResponse(
        example: ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员', 'mobile_phone' => '18888888888', 'email' => 'admin@example.com', 'status' => 1, 'dept_id' => 1],
        description: '获取管理员详情 - 数组模式'
    )]
    public function testDataArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'mobile_phone' => '18888888888',
                'email' => 'admin@example.com',
                'status' => 1,
                'dept_id' => 1,
            ],
        ]);
    }

    // ==================== ListResponse 多种场景 ====================

    /**
     * ListResponse - Class 模式（推荐）
     */
    #[OA\Get(
        path: '/test/list-class',
        summary: 'ListResponse - Class 模式（使用Schema类）',
        tags: ['Response测试']
    )]
    #[ListResponse(
        schema: AdminSchema::class,
        description: '获取管理员列表 - Class 模式'
    )]
    public function testListClass(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'user_name' => 'admin1', 'real_name' => '管理员1', 'mobile_phone' => '18888888881', 'email' => 'admin1@example.com', 'status' => 1, 'dept_id' => 1],
                ['id' => 2, 'user_name' => 'admin2', 'real_name' => '管理员2', 'mobile_phone' => '18888888882', 'email' => 'admin2@example.com', 'status' => 1, 'dept_id' => 2],
            ],
        ]);
    }

    /**
     * ListResponse - JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/list-json',
        summary: 'ListResponse - JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[ListResponse(
        example: '[{"id": 1,"name": "管理员","code": "admin"},{"id": 2,"name": "普通用户","code": "user"}]',
        description: '获取角色列表 - JSON 模式'
    )]
    public function testListJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '管理员', 'code' => 'admin'],
                ['id' => 2, 'name' => '普通用户', 'code' => 'user'],
            ],
        ]);
    }

    /**
     * ListResponse - 数组对象模式
     */
    #[OA\Get(
        path: '/test/list-array',
        summary: 'ListResponse - 数组对象模式',
        tags: ['Response测试']
    )]
    #[ListResponse(
        example: [['id' => 1, 'name' => '管理员', 'code' => 'admin'], ['id' => 2, 'name' => '普通用户', 'code' => 'user']],
        description: '获取角色列表 - 数组模式'
    )]
    public function testListArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '管理员', 'code' => 'admin'],
                ['id' => 2, 'name' => '普通用户', 'code' => 'user'],
            ],
        ]);
    }

    // ==================== PageResponse 多种场景 ====================

    /**
     * PageResponse - Class 模式（推荐）
     */
    #[OA\Get(
        path: '/test/page-class',
        summary: 'PageResponse - Class 模式（使用Schema类）',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: AdminSchema::class,
        description: '获取管理员分页列表 - Class 模式',
        totalExample: 100,
        pageExample: 1,
        limitExample: 10
    )]
    public function testPageClass(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 100,
                'page' => 1,
                'limit' => 10,
                'items' => [
                    ['id' => 1, 'user_name' => 'admin1', 'real_name' => '管理员1', 'mobile_phone' => '18888888881', 'email' => 'admin1@example.com', 'status' => 1],
                    ['id' => 2, 'user_name' => 'admin2', 'real_name' => '管理员2', 'mobile_phone' => '18888888882', 'email' => 'admin2@example.com', 'status' => 1],
                ],
            ],
        ]);
    }

    /**
     * PageResponse - JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/page-json',
        summary: 'PageResponse - JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[PageResponse(
        example: '[{"id": 1,"user_name": "admin","real_name": "管理员"},{"id": 2,"user_name": "user","real_name": "普通用户"}]',
        description: '获取用户分页列表 - JSON 模式',
        totalExample: 50,
        pageExample: 1,
        limitExample: 10
    )]
    public function testPageJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 50,
                'page' => 1,
                'limit' => 10,
                'items' => [
                    ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'],
                    ['id' => 2, 'user_name' => 'user', 'real_name' => '普通用户'],
                ],
            ],
        ]);
    }

    /**
     * PageResponse - 数组对象模式
     */
    #[OA\Get(
        path: '/test/page-array',
        summary: 'PageResponse - 数组对象模式',
        tags: ['Response测试']
    )]
    #[PageResponse(
        example: [['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'], ['id' => 2, 'user_name' => 'user', 'real_name' => '普通用户']],
        totalExample: 200,
        pageExample: 2,
        limitExample: 20,
        description: '获取用户分页列表 - 数组模式'
    )]
    public function testPageArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 200,
                'page' => 2,
                'limit' => 20,
                'items' => [
                    ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'],
                    ['id' => 2, 'user_name' => 'user', 'real_name' => '普通用户'],
                ],
            ],
        ]);
    }

    // ==================== TreeResponse 多种场景 ====================

    /**
     * TreeResponse - Class 模式（推荐）
     */
    #[OA\Get(
        path: '/test/tree-class',
        summary: 'TreeResponse - Class 模式（使用Schema类）',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        schema: DeptSchema::class,
        description: '获取部门树 - Class 模式'
    )]
    public function testTreeClass(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '总部', 'children' => []],
                ['id' => 2, 'name' => '技术部', 'children' => []],
            ],
        ]);
    }

    /**
     * TreeResponse - JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/tree-json',
        summary: 'TreeResponse - JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        example: '[{"id": 1,"name": "系统管理","children": [{"id": 11,"name": "用户管理","children": []}]},{"id": 2,"name": "开发部","children": []}]',
        description: '获取部门树 - JSON 模式'
    )]
    public function testTreeJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '系统管理', 'children' => [['id' => 11, 'name' => '用户管理', 'children' => []]]],
                ['id' => 2, 'name' => '开发部', 'children' => []],
            ],
        ]);
    }

    /**
     * TreeResponse - 数组对象模式
     */
    #[OA\Get(
        path: '/test/tree-array',
        summary: 'TreeResponse - 数组对象模式',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        example: [
            ['id' => 1, 'name' => '系统管理', 'children' => [['id' => 11, 'name' => '用户管理', 'children' => []]]],
            ['id' => 2, 'name' => '开发部', 'children' => []]
        ],
        description: '获取部门树 - 数组模式'
    )]
    public function testTreeArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '系统管理', 'children' => [['id' => 11, 'name' => '用户管理', 'children' => []]]],
                ['id' => 2, 'name' => '开发部', 'children' => []],
            ],
        ]);
    }

    // ==================== SimpleResponse 多种场景 ====================

    /**
     * SimpleResponse - 无示例模式
     */
    #[OA\Get(
        path: '/test/simple-none',
        summary: 'SimpleResponse - 无示例模式',
        tags: ['Response测试']
    )]
    #[SimpleResponse(description: '删除成功')]
    public function testSimpleNone(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => null,
        ]);
    }

    /**
     * SimpleResponse - JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/simple-json',
        summary: 'SimpleResponse - JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[SimpleResponse(
        example: 'null',
        description: '删除成功 - JSON 模式'
    )]
    public function testSimpleJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => null,
        ]);
    }

    /**
     * SimpleResponse - 值模式
     */
    #[OA\Get(
        path: '/test/simple-value',
        summary: 'SimpleResponse - 值模式',
        tags: ['Response测试']
    )]
    #[SimpleResponse(
        example: 1,
        description: '删除成功 - 值模式'
    )]
    public function testSimpleValue(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => 1,
        ]);
    }

    // ==================== ResultResponse 多种场景 ====================

    /**
     * ResultResponse - JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/result-json',
        summary: 'ResultResponse - JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[ResultResponse(
        example: '{"flag": true}',
        description: '检查结果 - JSON 模式'
    )]
    public function testResultJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => ['flag' => true],
        ]);
    }

    /**
     * ResultResponse - 数组对象模式
     */
    #[OA\Get(
        path: '/test/result-array',
        summary: 'ResultResponse - 数组对象模式',
        tags: ['Response测试']
    )]
    #[ResultResponse(
        example: ['flag' => true],
        description: '检查结果 - 数组模式'
    )]
    public function testResultArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => ['flag' => true],
        ]);
    }

    /**
     * ResultResponse - 对象模式
     */
    #[OA\Get(
        path: '/test/result-object',
        summary: 'ResultResponse - 对象模式',
        tags: ['Response测试']
    )]
    #[ResultResponse(
        example: ['success' => true, 'message' => '操作成功', 'count' => 5],
        description: '检查结果 - 对象模式'
    )]
    public function testResultObject(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => ['success' => true, 'message' => '操作成功', 'count' => 5],
        ]);
    }

    // ==================== 多状态码场景 ====================

    /**
     * DataResponse - 多状态码场景
     */
    #[OA\Get(
        path: '/test/data-multiple',
        summary: 'DataResponse - 多状态码场景',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: AdminSchema::class,
        description: '获取用户成功',
        response: 200
    )]
    #[DataResponse(
        example: ['error' => '用户不存在'],
        description: '用户未找到',
        response: 404
    )]
    #[DataResponse(
        example: ['error' => '无权访问'],
        description: '权限不足',
        response: 403
    )]
    public function testDataMultiple(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'mobile_phone' => '18888888888',
                'email' => 'admin@example.com',
                'status' => 1,
                'dept_id' => 1,
            ],
        ]);
    }

    /**
     * PageResponse - 多状态码场景
     */
    #[OA\Get(
        path: '/test/page-multiple',
        summary: 'PageResponse - 多状态码场景',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: AdminSchema::class,
        description: '获取分页列表成功',
        response: 200
    )]
    #[PageResponse(
        example: ['error' => '参数错误'],
        description: '参数错误',
        response: 400
    )]
    public function testPageMultiple(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 100,
                'page' => 1,
                'limit' => 10,
                'items' => [
                    ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'],
                ],
            ],
        ]);
    }

    /**
     * SimpleResponse - 多状态码场景
     */
    #[OA\Get(
        path: '/test/simple-multiple',
        summary: 'SimpleResponse - 多状态码场景',
        tags: ['Response测试']
    )]
    #[SimpleResponse(description: '操作成功', response: 200)]
    #[SimpleResponse(description: '参数错误', response: 400)]
    #[SimpleResponse(description: '未授权', response: 401)]
    #[SimpleResponse(description: '服务器错误', response: 500)]
    public function testSimpleMultiple(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => null,
        ]);
    }

    /**
     * ListResponse - 多状态码场景
     */
    #[OA\Get(
        path: '/test/list-multiple',
        summary: 'ListResponse - 多状态码场景',
        tags: ['Response测试']
    )]
    #[ListResponse(
        schema: AdminSchema::class,
        description: '获取列表成功',
        response: 200
    )]
    #[ListResponse(
        example: ['error' => '无权访问'],
        description: '权限不足',
        response: 403
    )]
    public function testListMultiple(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'],
            ],
        ]);
    }

    /**
     * TreeResponse - 多状态码场景
     */
    #[OA\Get(
        path: '/test/tree-multiple',
        summary: 'TreeResponse - 多状态码场景',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        schema: DeptSchema::class,
        description: '获取树成功',
        response: 200
    )]
    #[TreeResponse(
        example: ['error' => '树结构不存在'],
        description: '树结构不存在',
        response: 404
    )]
    public function testTreeMultiple(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '总部', 'children' => []],
            ],
        ]);
    }

    // ==================== 复杂场景 ====================

    /**
     * 复杂对象响应 - Schema + 详细示例
     */
    #[OA\Get(
        path: '/test/complex',
        summary: '复杂对象响应 - Schema + 详细示例',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: AdminSchema::class,
        example: [
            'id' => 999,
            'user_name' => 'complex_test_user',
            'real_name' => '复杂测试用户',
            'mobile_phone' => '13999999999',
            'email' => 'complex@example.com',
            'status' => 1,
            'dept_id' => 1,
            'avatar' => '/upload/avatar.jpg',
            'is_super' => 0,
            'is_tenant_admin' => 1,
        ],
        description: '返回复杂对象数据（Schema+详细示例）'
    )]
    public function testComplex(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 999,
                'user_name' => 'complex_test_user',
                'real_name' => '复杂测试用户',
                'mobile_phone' => '13999999999',
                'email' => 'complex@example.com',
                'status' => 1,
                'dept_id' => 1,
                'avatar' => '/upload/avatar.jpg',
                'is_super' => 0,
                'is_tenant_admin' => 1,
            ],
        ]);
    }

    /**
     * 分页响应 - 自定义分页参数
     */
    #[OA\Get(
        path: '/test/page-custom',
        summary: '分页响应 - 自定义分页参数',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: AdminSchema::class,
        totalExample: 1000,
        pageExample: 5,
        limitExample: 50,
        description: '获取大页码分页列表'
    )]
    public function testPageCustom(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 1000,
                'page' => 5,
                'limit' => 50,
                'items' => [
                    ['id' => 201, 'user_name' => 'admin201', 'real_name' => '管理员201'],
                    ['id' => 202, 'user_name' => 'admin202', 'real_name' => '管理员202'],
                ],
            ],
        ]);
    }

    // ==================== Schema JSON 字符串场景 ====================

    /**
     * DataResponse - Schema JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/data-schema-json',
        summary: 'DataResponse - Schema JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: '{"id": 1,"user_name": "admin","real_name": "管理员","mobile_phone": "18888888888","email": "admin@example.com","status": 1,"dept_id": 1}',
        description: '获取管理员详情 - Schema JSON 模式'
    )]
    public function testDataSchemaJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'mobile_phone' => '18888888888',
                'email' => 'admin@example.com',
                'status' => 1,
                'dept_id' => 1,
            ],
        ]);
    }

    /**
     * ListResponse - Schema JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/list-schema-json',
        summary: 'ListResponse - Schema JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[ListResponse(
        schema: '[{"id": 1,"name": "管理员","code": "admin"},{"id": 2,"name": "普通用户","code": "user"}]',
        description: '获取角色列表 - Schema JSON 模式'
    )]
    public function testListSchemaJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '管理员', 'code' => 'admin'],
                ['id' => 2, 'name' => '普通用户', 'code' => 'user'],
            ],
        ]);
    }

    /**
     * PageResponse - Schema JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/page-schema-json',
        summary: 'PageResponse - Schema JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: '[{"id": 1,"user_name": "admin","real_name": "管理员"},{"id": 2,"user_name": "user","real_name": "普通用户"}]',
        totalExample: 50,
        pageExample: 1,
        limitExample: 10,
        description: '获取用户分页列表 - Schema JSON 模式'
    )]
    public function testPageSchemaJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 50,
                'page' => 1,
                'limit' => 10,
                'items' => [
                    ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'],
                    ['id' => 2, 'user_name' => 'user', 'real_name' => '普通用户'],
                ],
            ],
        ]);
    }

    /**
     * TreeResponse - Schema JSON 字符串模式
     */
    #[OA\Get(
        path: '/test/tree-schema-json',
        summary: 'TreeResponse - Schema JSON 字符串模式',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        schema: '[{"id": 1,"name": "系统管理","children": [{"id": 11,"name": "用户管理","children": []}]},{"id": 2,"name": "开发部","children": []}]',
        description: '获取部门树 - Schema JSON 模式'
    )]
    public function testTreeSchemaJson(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '系统管理', 'children' => [['id' => 11, 'name' => '用户管理', 'children' => []]]],
                ['id' => 2, 'name' => '开发部', 'children' => []],
            ],
        ]);
    }


    /**
     * 列表响应 - 空列表场景
     */
    #[OA\Get(
        path: '/test/list-empty',
        summary: '列表响应 - 空列表场景',
        tags: ['Response测试']
    )]
    #[ListResponse(
        schema: AdminSchema::class,
        example: [],
        description: '空列表场景'
    )]
    public function testListEmpty(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [],
        ]);
    }

    /**
     * 分页响应 - 空分页场景
     */
    #[OA\Get(
        path: '/test/page-empty',
        summary: '分页响应 - 空分页场景',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: AdminSchema::class,
        totalExample: 0,
        pageExample: 1,
        limitExample: 10,
        description: '空分页场景'
    )]
    public function testPageEmpty(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 0,
                'page' => 1,
                'limit' => 10,
                'items' => [],
            ],
        ]);
    }

    /**
     * 树响应 - 空树场景
     */
    #[OA\Get(
        path: '/test/tree-empty',
        summary: '树响应 - 空树场景',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        schema: DeptSchema::class,
        example: [],
        description: '空树场景'
    )]
    public function testTreeEmpty(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [],
        ]);
    }

    // ==================== Schema 数组对象模式 ====================

    /**
     * DataResponse - Schema 数组对象模式
     */
    #[OA\Get(
        path: '/test/data-schema-array',
        summary: 'DataResponse - Schema 数组对象模式',
        tags: ['Response测试']
    )]
    #[DataResponse(
        schema: ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员', 'mobile_phone' => '18888888888', 'email' => 'admin@example.com', 'status' => 1, 'dept_id' => 1],
        description: '获取管理员详情 - Schema 数组对象模式'
    )]
    public function testDataSchemaArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'mobile_phone' => '18888888888',
                'email' => 'admin@example.com',
                'status' => 1,
                'dept_id' => 1,
            ],
        ]);
    }

    /**
     * ListResponse - Schema 数组对象模式
     */
    #[OA\Get(
        path: '/test/list-schema-array',
        summary: 'ListResponse - Schema 数组对象模式',
        tags: ['Response测试']
    )]
    #[ListResponse(
        schema: [['id' => 1, 'name' => '管理员', 'code' => 'admin'], ['id' => 2, 'name' => '普通用户', 'code' => 'user']],
        description: '获取角色列表 - Schema 数组对象模式'
    )]
    public function testListSchemaArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '管理员', 'code' => 'admin'],
                ['id' => 2, 'name' => '普通用户', 'code' => 'user'],
            ],
        ]);
    }

    /**
     * PageResponse - Schema 数组对象模式
     */
    #[OA\Get(
        path: '/test/page-schema-array',
        summary: 'PageResponse - Schema 数组对象模式',
        tags: ['Response测试']
    )]
    #[PageResponse(
        schema: [['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'], ['id' => 2, 'user_name' => 'user', 'real_name' => '普通用户']],
        totalExample: 50,
        pageExample: 1,
        limitExample: 10,
        description: '获取用户分页列表 - Schema 数组对象模式'
    )]
    public function testPageSchemaArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'total' => 50,
                'page' => 1,
                'limit' => 10,
                'items' => [
                    ['id' => 1, 'user_name' => 'admin', 'real_name' => '管理员'],
                    ['id' => 2, 'user_name' => 'user', 'real_name' => '普通用户'],
                ],
            ],
        ]);
    }

    /**
     * TreeResponse - Schema 数组对象模式
     */
    #[OA\Get(
        path: '/test/tree-schema-array',
        summary: 'TreeResponse - Schema 数组对象模式',
        tags: ['Response测试']
    )]
    #[TreeResponse(
        schema: [['id' => 1, 'name' => '系统管理', 'children' => [['id' => 11, 'name' => '用户管理', 'children' => []]]], ['id' => 2, 'name' => '开发部', 'children' => []]],
        description: '获取部门树 - Schema 数组对象模式'
    )]
    public function testTreeSchemaArray(Request $request): Response
    {
        return json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                ['id' => 1, 'name' => '系统管理', 'children' => [['id' => 11, 'name' => '用户管理', 'children' => []]]],
                ['id' => 2, 'name' => '开发部', 'children' => []],
            ],
        ]);
    }

}

