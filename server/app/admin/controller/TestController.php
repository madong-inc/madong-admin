<?php
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

namespace app\admin\controller;

use app\common\services\system\SystemUserService;
use Closure;
use madong\helper\Snowflake;
use madong\services\email\MessagePushService;
use madong\services\route\RouteOrganizerService;
use madong\utils\Json;
use madong\utils\Util;
use support\Container;
use support\Request;
use support\View;
use Webman\Push\Api;
use Webman\Route;

class TestController
{

    public mixed $service;

    public function __construct()
    {

        $this->service = Container::make(SystemUserService::class);

    }

    public function index(Request $request): \support\Response
    {
        $organizerRoute = new RouteOrganizerService();
        $data           = $organizerRoute->getRoutes();

//        $result         = $organizerRoute->search($data, 'directory');
        return Json::success('ok', $data);
//        try {
//
//            Util::reloadWebman();
//            return Json::success('重启成功');
//            $data = $this->service->selectList([], '*', 1, 10, '', [], true);
//            return Json::success('ok', $data->toArray());
//        } catch (\Throwable $e) {
//            return Json::fail($e->getMessage());
//        }

//        $api = new Api(
//            'http://127.0.0.1:3232',
//            config('plugin.webman.push.app.app_key'),
//            config('plugin.webman.push.app.app_secret')
//        );
//        // 给订阅 admin 的所有客户端推送 message 事件的消息
//        $return_ret = [
//            'event'   => 'message',
//            'message' => '新消息通知',
//            'data'    => [
//                [
//                    'id'        => 1,
//                    'uid'       => 2,
//                    'avatar'    => '',
//                    'is_read'   => false,
//                    'title'     => '系统消息',
//                    'message'   => '欢迎使用MadongPRO框架',
//                    'date'      => date('Y-m-d H:i:s'),
//                    'send_user' => [
//                        'nickname' => '系统管理员',
//                        'avatar'   => '',
//                    ],
//                ],
//            ],
//        ];
//        $api->trigger('admin', 'message', $return_ret);
        MessagePushService::broadcastMessage();
    }

    /**
     * 路由数据重构输出children格式
     *
     * @param array $routes
     *
     * @return array
     */
    private function buildRouteTree(array $routes): array
    {
        $tree = [];
        foreach ($routes as $route) {
            $parts   = explode('.', $route['name']);
            $current = &$tree;
            $depth   = 0;

            while ($depth < count($parts)) {
                $isLeaf      = ($depth === count($parts) - 1);
                $currentPart = $parts[$depth];

                // 节点类型判断
                $nodeType = $isLeaf ? 'route' : 'directory';

                // 合并路由数据
                $nodeData = ($isLeaf)
                    ? array_merge($route, ['name' => $currentPart, 'type' => $nodeType])
                    : ['type' => $nodeType, 'name' => $currentPart, 'children' => []];

                // 搜索已存在节点
                $existIndex = array_search($currentPart, array_column($current, 'name'));

                if ($existIndex === false) {
                    $current[]  = $nodeData;
                    $existIndex = array_key_last($current);
                }

                // 指针下钻（仅目录需要）
                if (!$isLeaf) {
                    $current = &$current[$existIndex]['children'];
                }

                $depth++;
            }
        }
        return $tree;
    }

    private function buildPidTree(array $routes): array
    {
        $nodes     = [];
        $idCounter = 1;
        $pathMap   = ['root' => ['id' => 0]]; // 根节点锚点

        foreach ($routes as $route) {
            $parts       = explode('.', $route['name']);
            $currentPath = 'root';
            $parentId    = 0;

            foreach ($parts as $part) {
                $nodePath = $currentPath . '.' . $part;

                // 节点已存在检测
                if (!isset($pathMap[$nodePath])) {
                    $isLeaf = ($part === end($parts));

                    $node = [
                            'id'   => $idCounter,
                            'pid'  => $parentId,
                            'name' => $part,
                            'type' => $isLeaf ? 'route' : 'directory',
                        ] + ($isLeaf ? $route : []); // 合并路由元数据

                    $nodes[]            = $node;
                    $pathMap[$nodePath] = [
                        'id'  => $idCounter,
                        'pid' => $parentId,
                    ];
                    $idCounter++;
                }

                // 更新指针
                $parentId    = $pathMap[$nodePath]['id'];
                $currentPath = $nodePath;
            }
        }

        return $nodes;
    }

    private function buildSnowflakeTree(array $routes, int $workerId = 1, int $dataCenterId = 1): array
    {
        $generator = new Snowflake($workerId, $dataCenterId);
        $nodes     = [];
        $pathMap   = ['root' => ['id' => $generator->nextId(), 'leavel' => 0]]; // 初始化根节点层级

        foreach ($routes as $route) {
            $parts         = explode('.', $route['name']);
            $currentPath   = 'root';
            $parentId      = $pathMap['root']['id'];
            $currentLeavel = 0; // 当前层级计数器

            foreach ($parts as $index => $part) {
                $nodePath = $currentPath . '.' . $part;
                $isLeaf   = ($index === count($parts) - 1);

                // 节点存在性检测
                if (!isset($pathMap[$nodePath])) {
                    $nodeId = $generator->nextId();
                    $node   = [
                        'id'     => $nodeId,
                        'pid'    => $parentId,
                        'name'   => $part,       // 仅存储当前层级名称
                        'leavel' => $currentLeavel + 1, // 层级=父层级+1
                        'type'   => $isLeaf ? 'route' : 'directory',
                    ];

                    // 合并路由元数据
                    if ($isLeaf) {
                        unset($route['name']); // 移除冗余字段
                        $node = array_merge($node, $route);
                    }

                    $nodes[]            = $node;
                    $pathMap[$nodePath] = [
                        'id'     => $nodeId,
                        'pid'    => $parentId,
                        'leavel' => $currentLeavel + 1,
                    ];
                }

                // 更新指针
                $parentId      = $pathMap[$nodePath]['id'];
                $currentLeavel = $pathMap[$nodePath]['leavel']; // 继承当前层级
                $currentPath   = $nodePath;
            }
        }
        return $nodes;
    }

}



