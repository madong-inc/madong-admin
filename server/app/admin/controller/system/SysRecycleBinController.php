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
use app\common\services\system\SysRecycleBinService;
use madong\admin\utils\Json;
use support\Container;
use support\Request;

class SysRecycleBinController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SysRecycleBinService::class);
    }

    /**
     * 恢复数据
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function restore(Request $request): \support\Response
    {
        try {
            $id = $request->input('id');
            $this->service->restoreRecycleBin($id);
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
