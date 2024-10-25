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

namespace app\admin\controller\system;

use app\admin\controller\Crud;
use app\admin\validate\system\SystemMenuValidate;
use app\services\system\SystemMenuService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemMenuController extends Crud
{

    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemMenuService::class);
        $this->validate = Container::make(SystemMenuValidate::class);
    }

    /**
     * 数据列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $where[] = ['enabled', '=', 1];
            $list    = $this->service->selectList($where, $field, $page, $limit, $order, [], true)->toArray();
            return Json::success('ok', $list);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 获取菜单树
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function buildMenuTree(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $list = $this->service->menuTree($where);
            return Json::success('ok', $list);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

}
