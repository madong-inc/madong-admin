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
use app\services\system\SystemDictService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemDictController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SystemDictService::class);
    }




    public function getByDictType(Request $request){


    }


    /**
     * 获取枚举字典列表
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
     * 自定义枚举-添加对应的目录path就会扫描
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function customDictList(Request $request): \support\Response
    {
        return Json::success('ok', []);
    }

}
