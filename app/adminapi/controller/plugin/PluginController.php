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

use app\adminapi\controller\Base;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\service\core\plugin\PluginDownloadService;
use app\service\core\plugin\PluginInstallService;
use app\service\core\plugin\PluginRemoteService;
use app\service\core\plugin\PluginUninstallService;
use app\service\core\plugin\PluginService;
use core\tool\Sse;
use madong\swagger\annotation\response\PageResponse;
use madong\swagger\attribute\Permission;
use core\tool\Json;
use madong\swagger\annotation\response\SimpleResponse;
use OpenApi\Attributes as OA;
use support\annotation\Middleware;
use support\Container;
use support\Request;
use support\Response;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class PluginController extends Base
{

    #[OA\Get(
        path: '/plugin',
        summary: '列表',
        tags: ['模块市场'],
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
                schema: new OA\Schema(type: 'integer', default: 10)
            ),
            new OA\Parameter(
                name: 'type',
                description: '模块类型（module: 系统模块, plugin: 插件）',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['module', 'plugin'])
            ),
            new OA\Parameter(
                name: 'status',
                description: '状态(0:禁用,1:启用)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', enum: [0, 1])
            ),
            new OA\Parameter(
                name: 'keyword',
                description: '搜索关键词',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'installed',
                description: '是否已安装(0:未安装,1:已安装)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', enum: [0, 1])
            ),
        ],
    )]
    #[PageResponse(schema: [], example: [[
                                             'id'                    => 3,
                                             'code'                  => 'official',
                                             'name'                  => 'official',
                                             'type'                  => 'madong',
                                             'version'               => '1.0.0',
                                             'status'                => 1,
                                             'description'           => '官方应用插件',
                                             'detail_description'    => '',
                                             'author'                => 'Madong',
                                             'cover'                 => '',
                                             'poster'                => '',
                                             'price'                 => 0,
                                             'downloads'             => 0,
                                             'rating'                => 0,
                                             'created_at'            => '2026-03-19 16:26:19',
                                             'updated_at'            => '2026-03-19 16:26:19',
                                             'update_time'           => '',
                                             'update_logs'           => [],
                                             'category'              => null,
                                             'category_name'         => '',
                                             'tags'                  => [],
                                             'is_new'                => 0,
                                             'is_hot'                => 0,
                                             'purchased'             => 0,
                                             'installed'             => 0,
                                             'manual_uninstall'      => 0,
                                             'composer_dependencies' => [],
                                             'npm_dependencies'      => [],
                                             'is_installed'          => 1,
                                             'installed_version'     => '1.0.0',
                                             'has_update'            => 0,
                                             'is_purchased'          => 1,
                                             'is_downloaded'         => 1,
                                             'can_uninstall'         => 1,
                                             'can_download'          => 1,
                                             'undeletable'           => 1,
                                             'version_info'          => [
                                                 'remote'     => [
                                                     'version'      => '1.0.0',
                                                     'release_date' => '2026-03-19 16:26:19',
                                                 ],
                                                 'local'      => [
                                                     'version'      => '1.0.0',
                                                     'install_date' => '2026-03-19 16:26:19',
                                                 ],
                                                 'comparison' => [
                                                     'needs_update'       => 0,
                                                     'is_latest'          => 1,
                                                     'version_difference' => null,
                                                 ],
                                             ],
                                         ]])]
                                             #[Permission(code: 'plugin:module:list')]
    public function index(Request $request): \support\Response
    {
        try {
            $category = $request->input('category', null);
            $page     = $request->input('page', 1);
            $limit    = $request->input('limit', 15);
            $type     = $request->input('type');
            $keyword  = $request->input('keyword');
            /** @var  PluginService $service */
            $service = Container::make(PluginService::class);
            $result  = $service->getList($category, $type, $keyword, $page, $limit);
            return Json::success('common.operation.success', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), []);
        }
    }

    #[OA\Get(
        path: '/plugin/{name}/download',
        summary: '下载模块',
        tags: ['模块市场'],
    )]
    #[OA\Parameter(
        name: 'name',
        description: '模块标识',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function download(Request $request): \support\Response
    {
        try {
            $pluginCode = $request->route->param('name');

            // 1. 获取下载链接
            $remoteService = Container::make(PluginRemoteService::class);
            $authCode     = config('madong.auth_code');
            $secretKey    = config('madong.auth_secret');

            $downloadInfo = $remoteService->getRemoteDownloadService($authCode, $secretKey, $pluginCode, 'latest');
            if (empty($downloadInfo['url'])) {
                return Json::fail('获取下载链接失败');
            }

            // 2. 下载并解压插件
            $downloadService = Container::make(PluginDownloadService::class);
            $result        = $downloadService->downloadAndExtract($pluginCode, $downloadInfo['url']);

            return Json::success('下载成功', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/plugin/detail/{name}',
        summary: '获取模块详情',
        tags: ['模块市场'],
    )]
    #[OA\Parameter(
        name: 'name',
        description: '模块标识',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function detail(Request $request): \support\Response
    {
        try {
            $moduleName = $request->route->param('name');
            /** @var PluginService $service */
            $service = Container::make(PluginService::class);
            $result  = $service->getList(null, null, $moduleName);
            return Json::success('common.operation.success', $result['items'][0]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), []);
        }
    }

    #[OA\Get(
        path: '/plugin/{name}/upgrade-logs',
        summary: '获取模块升级日志',
        tags: ['模块市场'],
    )]
    #[OA\Parameter(
        name: 'name',
        description: '模块标识',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function upgradeLogs(Request $request): \support\Response
    {
        try {
            $moduleName = $request->route->param('name');
            /** @var PluginService $service */
            $service = Container::make(PluginService::class);
            $result  = $service->getUpgradeLogs($moduleName);

            return Json::success('common.operation.success', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), []);
        }
    }

    #[OA\Get(
        path: '/plugin/check-environment',
        summary: '环境检测',
        tags: ['模块市场'],
    )]
    #[OA\Parameter(
        name: 'name',
        description: '模块标识',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function checkEnvironment(Request $request): \support\Response
    {
        try {
            $name = $request->input('name');
            /** @var PluginInstallService $service */
            $service = Container::make(PluginInstallService::class);
            $result  = $service->installCheck($name);

            // 提取路径列表和状态
            $paths = [];
            foreach ($result['checks'] as $check) {
                if (isset($check['path'])) {
                    // 只展示从madong开始的路径
                    $path      = $check['path'];
                    $madongPos = strpos(strtolower($path), 'madong');
                    if ($madongPos !== false) {
                        // 截取从madong开始的路径
                        $path = substr($path, $madongPos);
                        // 确保路径格式正确，madong后面应该有目录分隔符
                        if (substr($path, 6, 1) !== DIRECTORY_SEPARATOR) {
                            $path = 'madong' . DIRECTORY_SEPARATOR . substr($path, 6);
                        }
                    }
                    $paths[] = [
                        'path'        => $path,
                        'requirement' => $check['permission_type'] ?? 'readable',
                        'status'      => $check['status'] === 'success' ? 'success' : 'error',
                    ];
                }
            }

            return Json::success('common.operation.success', [
                'paths'          => $paths,
                'overall_status' => $result['overall_status'],
                'message'        => $result['message'],
            ]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), []);
        }
    }

    #[OA\Get(
        path: '/plugin/install',
        summary: '插件安装',
        tags: ['模块市场'],
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function install(Request $request): void
    {
        $connection = $request->connection;
        $code       = $request->input('code');
        $mode       = $request->input('mode', 'local');

        // 验证参数
        if (empty($code)) {
            $connection->send(new Response(200, [
                'Content-Type'                     => 'text/event-stream',
                'Cache-Control'                    => 'no-cache',
                'Connection'                       => 'keep-alive',
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Expose-Headers'    => 'Content-Type',
            ], "\r\n"));
            $connection->send(\core\tool\Sse::error('参数错误：缺少code参数'));
            return;
        }

        // 发送SSE响应头
        $connection->send(new Response(200, [
            'Content-Type'                     => 'text/event-stream',
            'Cache-Control'                    => 'no-cache',
            'Connection'                       => 'keep-alive',
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Expose-Headers'    => 'Content-Type',
        ], "\r\n"));

        try {
            // 发送开始安装消息
            $connection->send(Sse::progress('开始安装插件 ' . $code, 0));

            // 调用安装服务
            $generator = (new PluginInstallService())->install($code, $mode);
            foreach ($generator as $chunk) {
                $connection->send($chunk);
            }
        } catch (\Exception $e) {
            // 发送错误消息
            $connection->send(Sse::error('安装失败：' . $e->getMessage()));
        }

    }

    #[OA\Get(
        path: '/plugin/uninstall',
        summary: '插件卸载（SSE流式）',
        tags: ['模块市场'],
    )]
    #[OA\Parameter(
        name: 'code',
        description: '插件编码',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function uninstall(Request $request): void
    {
        $connection = $request->connection;
        $code       = $request->input('code');
        // 验证参数
        if (empty($code)) {
            $connection->send(new Response(200, [
                'Content-Type'                     => 'text/event-stream',
                'Cache-Control'                    => 'no-cache',
                'Connection'                       => 'keep-alive',
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Expose-Headers'    => 'Content-Type',
            ], "\r\n"));
            $connection->send(Sse::error('参数错误：缺少code参数'));
            return;
        }

        // 发送SSE响应头
        $connection->send(new Response(200, [
            'Content-Type'                     => 'text/event-stream',
            'Cache-Control'                    => 'no-cache',
            'Connection'                       => 'keep-alive',
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Expose-Headers'    => 'Content-Type',
        ], "\r\n"));

        try {
            // 调用卸载服务（统一入口）
            $generator = (new PluginUninstallService())->uninstall($code);
            foreach ($generator as $chunk) {
                $connection->send($chunk);
            }
        } catch (\Exception $e) {
            // 发送错误消息
            $connection->send(Sse::error('卸载失败：' . $e->getMessage()));
        }
    }

    #[OA\Delete(
        path: '/plugin/{name}',
        summary: '插件删除',
        tags: ['模块市场'],
    )]
    #[OA\Parameter(
        name: 'name',
        description: '插件名称',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[Permission(code: 'plugin:delete')]
    #[SimpleResponse(schema: [], example: [])]
    public function destroy(Request $request): \support\Response
    {
        try {
            $name = $request->route->param('name');
            if (empty($name)) {
                return Json::fail('参数错误：缺少name参数', []);
            }

            /** @var PluginUninstallService $service */
            $service = Container::get(PluginUninstallService::class);
            $service->delete($name);
            return Json::success('common.operation.delete.success', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: '/plugin/select',
        summary: '获取已安装插件列表（用于下拉框）',
        tags: ['模块市场'],
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function select(Request $request): \support\Response
    {
        try {
            /** @var PluginService $service */
            $service = Container::make(PluginService::class);

            // 获取本地插件列表（包括已安装和未安装的）
            $localModules = $service->getLocalModules();

            // 过滤出已安装的插件
            $installedPlugins = array_filter($localModules, function ($module) {
                return $module['is_installed'] === true && $module['status'] == 1;
            });

            // 构建下拉框格式数据
            $selectItems = [];
            foreach ($installedPlugins as $plugin) {
                $selectItems[] = [
                    'value'       => $plugin['name'],
                    'label'       => $plugin['name'] . ' - ' . $plugin['description'],
                    'name'        => $plugin['name'],
                    'description' => $plugin['description'],
                    'version'     => $plugin['version'],
                    'type'        => $plugin['type'] ?? 'plugin',
                ];
            }

            return Json::success('common.operation.success', array_values($selectItems));
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), []);
        }
    }
}