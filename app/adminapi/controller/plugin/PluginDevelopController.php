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

namespace app\adminapi\controller\plugin;

use app\adminapi\controller\Crud;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\adminapi\validate\plugin\PluginValidate;
use app\service\admin\plugin\PluginDevelopService;
use madong\swagger\annotation\response\DataResponse;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\Permission;
use core\tool\Json;
use OpenApi\Attributes as OA;
use support\annotation\Middleware;
use support\Request;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class PluginDevelopController extends Crud
{

    public function __construct(PluginDevelopService $service, PluginValidate $validate)
    {
        $this->service  = $service;
        $this->validate = $validate;
    }

    /**
     * 插件列表
     * 扫描插件目录，获取type为madong:前缀的插件，同步到数据库并返回列表
     *
     * @param Request $request
     *
     * @return \support\Response
     */
    #[OA\Get(
        path: '/plugin/develop',
        summary: '插件列表',
        tags: ['插件开发'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: '页码',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: '每页数量',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 15)
            ),
            new OA\Parameter(
                name: 'title',
                description: '插件标题',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'author',
                description: '作者',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'status',
                description: '状态',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
        ]
    )]
    #[Permission(code: 'plugin:develop:list')]
    #[PageResponse(schema: [], example: [[
                                             "id"           => "2032735331152958596",
                                             "name"        => "官网管理",
                                             "icon"         => null,
                                             "code"          => "official",
                                             "description"         => "官网管理插件，用于搭建和管理企业官方网站",
                                             "enabled"       => 1,
                                             "author"       => "Mr.April",
                                             "version"      => "1.0.0",
                                             "cover"        => null,
                                             "type"         => "madong:plugin",
                                             "support_app"  => "admin",
                                             "installed_at" => null,
                                             "created_at"   => 1773476844,
                                             "updated_at"   => 1773476844,
                                             "variables"    => null,
                                             "created_date" => "2026-03-14 16:27:24",
                                             "updated_date" => "2026-03-14 16:27:24",
                                         ]])]
    public function index(Request $request): \support\Response
    {
        try {
            // 使用标准模式：通过 selectInput 处理查询参数
            [$where, , $limit, , , $page] = $this->selectInput($request);

            // 调用服务层获取列表数据
            $result = $this->service->getList($where, $page, $limit);

            // 使用标准格式返回
            return Json::success($result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/plugin/develop/{id}',
        summary: '插件详情',
        tags: ['插件开发'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: '插件ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ]
    )]
    #[Permission(code: 'plugin:develop:read')]
    #[DataResponse(schema: [], example: [
        "id"           => "2032735331152958596",
        "title"        => "官网管理",
        "icon"         => null,
        "key"          => "official",
        "desc"         => "官网管理插件，用于搭建和管理企业官方网站",
        "status"       => 1,
        "author"       => "Mr.April",
        "version"      => "1.0.0",
        "cover"        => null,
        "type"         => "madong:plugin",
        "support_app"  => "admin",
        "installed_at" => null,
        "created_at"   => 1773476844,
        "updated_at"   => 1773476844,
        "variables"    => null,
        "created_date" => "2026-03-14 16:27:24",
        "updated_date" => "2026-03-14 16:27:24",
    ])]
    public function show(Request $request): \support\Response
    {
        try {
            $id     = $request->route->param('id');
            $plugin = $this->service->read($id);
            if (!$plugin) {
                return Json::fail('插件不存在');
            }
            return Json::success($plugin->toArray());
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 创建插件
     *
     * @param Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/plugin/develop',
        summary: '创建插件',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'key'],
                properties: [
                    new OA\Property(property: 'title', description: '插件标题', type: 'string'),
                    new OA\Property(property: 'key', description: '插件标识（唯一值）', type: 'string'),
                    new OA\Property(property: 'desc', description: '插件描述', type: 'string'),
                    new OA\Property(property: 'author', description: '作者', type: 'string'),
                    new OA\Property(property: 'version', description: '版本', type: 'string', default: '1.0.0'),
                    new OA\Property(property: 'type', description: '类型', type: 'string', default: 'madong:plugin'),
                    new OA\Property(property: 'icon', description: '图标（base64）', type: 'string'),
                    new OA\Property(property: 'cover', description: '封面（base64）', type: 'string'),
                    new OA\Property(property: 'frontend_type', description: '前端类型', type: 'string', default: 'admin'),
                ]
            )
        ),
        tags: ['插件开发']
    )]
    #[Permission(code: 'plugin:develop:create')]
    #[SimpleResponse(schema: [], example: [])]
    public function store(Request $request): \support\Response
    {
        try {
            $this->validate->scene('store')->check($request->all());
            $this->service->store($request->all());
            return Json::success();
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 打包插件
     * 将后端、后台前端、前台前端复制到后端server/plugin/xxx/resource目录，并打包为zip
     *
     * @param Request $request
     *
     * @return \support\Response
     */
    #[OA\Post(
        path: '/plugin/develop/build',
        summary: '打包插件',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['plugin_key'],
                properties: [
                    new OA\Property(property: 'plugin_key', description: '插件标识', type: 'string'),
                ]
            )
        ),
        tags: ['插件开发']
    )]
    #[Permission(code: 'plugin:develop:build')]
    #[SimpleResponse(schema: [], example: [
        "plugin_key"   => "test",
        "zip_path"     => "D:\\MyProject\\private\\madong/server/runtime/adminapi/test.zip",
        "has_frontend" => false,
    ])]
    public function build(Request $request): \support\Response
    {
        try {
            $pluginKey = $request->input('plugin_key');
            if (empty($pluginKey)) {
                return Json::fail('插件标识不能为空');
            }

            $result = $this->service->buildPlugin($pluginKey);
            return Json::success('插件打包成功',$result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Put(
        path: '/plugin/develop/{id}',
        summary: '编辑插件',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', description: '插件标题', type: 'string'),
                    new OA\Property(property: 'desc', description: '插件描述', type: 'string'),
                    new OA\Property(property: 'author', description: '作者', type: 'string'),
                    new OA\Property(property: 'version', description: '版本', type: 'string'),
                    new OA\Property(property: 'icon', description: '图标（base64）', type: 'string'),
                    new OA\Property(property: 'cover', description: '封面（base64）', type: 'string'),
                ]
            )
        ),
        tags: ['插件开发'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: '插件ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ]
    )]
    #[Permission(code: 'plugin:develop:update')]
    #[SimpleResponse(schema: [], example: [])]
    public function update(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id');
            $this->service->update($id, $request->all());
            return Json::success('更新成功');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }
}