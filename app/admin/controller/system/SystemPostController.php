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
use madong\utils\Json;
use support\Container;
use support\Request;
use Throwable;

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
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function select(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $data = $this->service->selectList($where, $field, 0, 0, '', [], true)->toArray();
            return Json::success('ok', $data);
        } catch (Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }
}
