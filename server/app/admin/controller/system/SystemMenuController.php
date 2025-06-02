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
use app\common\services\system\SystemMenuService;
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
     * 批量添加菜单
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function batchStore(Request $request): \support\Response
    {
        try {
            $params = $request->input('menus', []);
            $data   = [];
            if (isset($this->validate) && $this->validate) {
                foreach ($params as $param) {
                    $data[] = $this->inputFilter($param);
                    if (!$this->validate->scene('batch-store')->check($param)) {
                        throw new \Exception($this->validate->getError());
                    }
                }
            }
            foreach ($data as $item) {
                $this->service->save($item);
            }
            return Json::success('ok');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
