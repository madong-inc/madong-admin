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
use app\common\services\system\SystemTenantPackageMenuService;
use app\common\services\system\SystemTenantPackageService;
use madong\exception\AdminException;
use madong\utils\Json;
use madong\utils\Util;
use support\Container;
use support\Request;

/**
 * @author Mr.April
 * @since  1.0
 */
class SystemTenantPackageController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SystemTenantPackageService::class);
//        $this->validate = Container::make(SystemDataSourceValidate::class);
    }

    /**
     * 添加
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function store(Request $request): \support\Response
    {
        try {

            $data = $this->inputFilter($request->all(), ['permissions']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->save($data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function update(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all(), ['permissions']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('update')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($data['id'],$data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 通过套餐ID获取权限ID集合
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function packageMenuIds(Request $request): \support\Response
    {
        try {
            $packageId                      = $request->input('package_id');
            $systemTenantPackageMenuService = Container::make(SystemTenantPackageMenuService::class);
            $data                           = $systemTenantPackageMenuService->getColumn(['package_id' => $packageId], 'menu_id');
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

}
