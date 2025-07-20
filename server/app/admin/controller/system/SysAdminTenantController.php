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
use app\admin\validate\system\SysAdminTenantValidate;
use app\common\model\platform\Tenant;
use app\common\scopes\global\TenantScope;
use app\common\services\system\SysAdminTenantService;
use core\exception\handler\AdminException;
use core\context\TenantContext;
use core\utils\Json;
use support\Container;
use support\Request;

class SysAdminTenantController extends Crud
{

    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SysAdminTenantService::class);
        $this->validate = Container::make(SysAdminTenantValidate::class);
    }

    /**
     * 通过id 获取租户中间表数据-无作用域
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function show(Request $request): \support\Response
    {
        try {
            $id      = $request->route->param('id');
            $service = Container::make(SysAdminTenantService::class);
            $result  = $service->get($id, null, [], '', [TenantScope::class]);
            return Json::success('ok', $result->toArray());
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 添加-关联租户
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function store(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $model = $this->service->save($data);
            if (empty($model)) {
                throw new AdminException('插入失败');
            }
            $pk = $model->getPk();
            return Json::success('ok', [$pk => $model->getAttribute($pk)]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function getPost(Request $request)
    {
        try {
            $deptId   = $request->input('dept_id', null);
            $format   = $request->input('format', null);
            $tenantId = $request->input('tenant_id', null);
            if (empty($tenantId)) {
                $tenantId = TenantContext::getTenantId();
            }
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $list            = $this->service->getTenantPostsNoScope($tenantId, $deptId);
            return call_user_func([$this, $format_function], $list, count($list));
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }

    public function getTenants(Request $request)
    {
        try {
            $format          = $request->input('format', null);
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $list            = $this->service->getTenantsNoScope();
            return call_user_func([$this, $format_function], $list, count($list));
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function getDept(Request $request)
    {
        try {
            $tenantId = $request->input('tenant_id', null);
            $format   = $request->input('format', null);
            if (empty($tenantId)) {
                $tenantId = TenantContext::getTenantId();
            }
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $list            = $this->service->getTenantDeptsNoScope($tenantId);
            return call_user_func([$this, $format_function], $list, count($list));
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function getRole(Request $request)
    {
        try {
            $tenantId = $request->input('tenant_id', null);
            $format   = $request->input('format', null);
            if (empty($tenantId)) {
                $tenantId = TenantContext::getTenantId();
            }
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $list            = $this->service->getTenantRolesNoScope($tenantId);
            return call_user_func([$this, $format_function], $list, count($list));
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 格式化下拉列表-重写
     *
     * @param $items
     *
     * @return \support\Response
     */
    protected function formatSelect($items): \support\Response
    {
        $formatted_items = [];
        foreach ($items as $item) {
            if ($item instanceof Tenant) {
                $formatted_items[] = [
                    'label' => $item->company_name,
                    'value' => $item->id,
                ];
                continue;
            }
            $formatted_items[] = [
                'label' => $item->title ?? $item->name ?? $item->real_name ?? $item->id,
                'value' => $item->id,
            ];
        }
        return Json::success('ok', $formatted_items);
    }

}
