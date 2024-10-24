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
use app\admin\validate\system\SystemDeptValidate;
use app\services\system\SystemDeptService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemDeptController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemDeptService::class);
        $this->validate = Container::make(SystemDeptValidate::class);
    }

    /**
     * 部门列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $data = $this->service->selectList($where, $field, 0, 0, '', [], false)->toArray();
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 获取部门树
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getDepartmentTree(Request $request): \support\Response
    {
        [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
        $format          = 'tree';
        $methods         = [
            'select'     => 'formatSelect',
            'tree'       => 'formatTree',
            'table_tree' => 'formatTableTree',
            'normal'     => 'formatNormal',
        ];
        $format_function = $methods[$format] ?? 'formatNormal';
        $total           = $this->service->count($where, true);
        $list            = $this->service->selectList($where, $field, 0, 0, '', [], true);
        return call_user_func([$this, $format_function], $list, $total);
    }

}
