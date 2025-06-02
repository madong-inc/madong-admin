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
use app\common\services\system\SystemConfigService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemConfigController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SystemConfigService::class);
    }

    /**
     * 获取用户配置
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getConfigInfo(Request $request): \support\Response
    {
        try {
            $groupCode = $request->input('group_code', '');
            $result    = $this->service->getConfigContentValue($groupCode);
            return Json::success('操作成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function store(Request $request): \support\Response
    {
        try {
            $data   = $request->all();
            $result = $this->service->batchUpdateConfig($data);
            return Json::success('保存成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }
}
