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

namespace app\common\services\system;

use app\common\dao\system\SystemRouteDao;
use app\common\model\system\SystemRoute;
use app\common\model\system\SystemRouteCate;
use madong\basic\BaseService;
use madong\services\route\RouteOrganizerService;
use support\Container;

/**
 * @method save(array $data)
 */
class SystemRuleService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemRouteDao::class);
    }

    /**
     * 路由同步数据库
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function sync()
    {
        try {
            $this->transaction(function () {
                $organize = new RouteOrganizerService();
                $routeRaw = $organize->getRoutes();
                //1.0导入分类
                $cate      = $organize->search($routeRaw, 'directory');
                $cateModel = new SystemRouteCate();
                $cateModel->query()->delete();
                foreach ($cate as $item) {
                    $row = [
                        'id'       => $item['id'],
                        'pid'      => $item['pid'],
                        'app_name' => 'admin',
                        'name'     => $item['name'],
                        'sort'     => $item['sort'] ?? 0,
                        'enabled'  => 1,
                    ];
                    $cateModel->create($row);
                }

                //2.0导入路由
                $list      = $organize->search($routeRaw, 'route');
                $listModel = new SystemRoute();
                $listModel->query()->delete();
                foreach ($list as $item) {
                    $row = [
                        'id'               => $item['id'],
                        'cate_id'          => $item['pid'],
                        'app_name'         => 'admin',
                        'name'             => $item['name'],
                        'describe'         => '',
                        'path'             => $item['path'],
                        'method'           => $item['methods'],
                        'file_path'        => $item['file_path'],
                        'action'           => $item['action'],
                        'query'            => [],
                        'header'           => [],
                        'request'          => '',
                        'request_type'     => '',
                        'response'         => [],
                        'request_example'  => [],
                        'response_example' => [],
                    ];
                    $listModel->create($row);
                }
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
