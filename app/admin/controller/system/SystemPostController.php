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
use app\admin\validate\system\SystemPostValidate;
use app\services\system\SystemPostService;
use support\Container;
use support\Request;

class SystemPostController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemPostService::class);
        $this->validate = Container::make(SystemPostValidate::class);
    }

    /**
     * 下拉列表
     * @param \support\Request $request
     *
     * @return \support\Request
     */
    public function select(Request $request):\support\Request
    {
        [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
        $format          = 'select';
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
