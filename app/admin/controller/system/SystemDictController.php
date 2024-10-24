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
use app\admin\validate\system\SystemDictValidate;
use app\services\system\SystemDictService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemDictController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemDictService::class);
        $this->validate = Container::make(SystemDictValidate::class);
    }

    /**
     * 字典获取
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getByDictType(Request $request): \support\Response
    {
        try {
            $dictType = $request->input('dict_type');
            $data     = $this->service->findItemsByCode($dictType);
            return Json::success('ok', $data);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 枚举字典列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function enumDictList(Request $request): \support\Response
    {
        $result = $this->service->scanEnums([app_path('enum')]);
        return Json::success('ok', $result);
    }

    /**
     * 自定义字典
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function customDictList(Request $request): \support\Response
    {
        return Json::success('ok', []);
    }

}
