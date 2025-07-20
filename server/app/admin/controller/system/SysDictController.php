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
use app\admin\validate\system\SysDictValidate;
use app\common\services\system\SysDictService;
use core\utils\Json;
use support\Container;
use support\Request;

class SysDictController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SysDictService::class);
        $this->validate = Container::make(SysDictValidate::class);
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
            //1.0优先扫描系统内置的枚举
            $data = $this->service->getEnumByNamespace($dictType,);
            if(empty($data)){
                //2.0如果系统没有获取数据字典
                $data = $this->service->findItemsByCode($dictType);
            }
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

        $enumDirectory = config('app.enum_scan_directories',[]);
        $result        = $this->service->scanEnums($enumDirectory);
        return Json::success('ok', $result);
    }


}
