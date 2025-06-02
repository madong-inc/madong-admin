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

namespace app\admin\controller\monitor;

use app\admin\controller\Crud;
use madong\services\monitor\ServerMonitor;
use madong\utils\Json;
use support\Container;
use support\Request;

class RedisController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
    }

    /**
     * Redis监控
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        try {
            $service = Container::make(ServerMonitor::class);
            $data    = $service->getRedisInfo();
            return Json::success('ok', $data);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }
}
