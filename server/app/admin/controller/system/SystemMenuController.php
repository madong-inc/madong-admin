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
use app\common\dao\system\SystemTenantPackageMenuDao;
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
     * 获取权限树-排除不在套餐内的
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function fetchPermissionTreeExclNonPackageIds(Request $request): \support\Response
    {
        try {
            $tenantEnabled = config('app.tenant_enabled', false);
            $tenantCache   = $request->getTenantId(true);
            if ($tenantEnabled && empty($tenantCache)) {
                throw new \Exception('参数异常，缺少租户信息');
            }
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $list = $this->service->selectList($where, $field, 0, 0, $order, [], false);
            if (empty($list)) {
                return Json::success('ok', []);
            }
            //非默认租户按套餐权限返回
            if ($tenantEnabled && (int)$tenantCache['is_default'] !== 1) {
                $packageMenu = Container::make(SystemTenantPackageMenuDao::class);
                $data        = $packageMenu->getColumn(['package_id' => $tenantCache['package_id']], 'menu_id');
                $newList        = $list->whereIn('id', $data);
                return $this->formatTableTree($newList, 0);
            }
            return $this->formatTableTree($list, 0);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
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
