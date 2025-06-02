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
use app\common\scopes\global\AccessScope;
use app\common\scopes\global\TenantScope;
use app\common\services\system\SystemTenantService;
use app\common\services\system\SystemUserService;
use madong\utils\Json;
use support\Container;
use support\Request;

/**
 * @author Mr.April
 * @since  1.0
 */
class SystemTenantController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SystemTenantService::class);
//        $this->validate = Container::make(SystemDataSourceValidate::class);
    }

    /***
     * 租户管理-列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $total           = $this->service->getCount($where);
            $list            = $this->service->selectList($where, $field, $page, $limit, $order, [], false, [
                TenantScope::class,
                AccessScope::class,
            ]);
            return call_user_func([$this, $format_function], $list, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
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

            $data = $this->inputFilter($request->all(), ['account', 'password', 'expired_date']);
            if (isset($this->validate) && $this->validate) {
                Container::make(SystemUserService::class);
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

    public function update(Request $request): \support\Response
    {
        try {

            $data = $this->inputFilter($request->all(), ['expired_date']);
            if (isset($this->validate) && $this->validate) {
                Container::make(SystemUserService::class);
                if (!$this->validate->scene('update')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($data['id'], $data);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function accountSets(Request $request): \support\Response
    {
        try {
            // 检查是否启用了租户模式
            $tenant_enabled = config('app.tenant_enabled', false);

            if (!$tenant_enabled) {
                // 如果未启用租户模式，直接返回空数组和相关配置
                $list = [];
                return Json::success('ok', compact('list', 'tenant_enabled'));
            }

            // 如果启用了账套模式，执行数据库查询
            $list = $this->service->selectList(['enabled' => 1], '*', 0, 0, '', [], false, [
                TenantScope::class,
                AccessScope::class,
            ])
                ->setVisible(['id', 'tenant_id', 'name'])
                ->toArray();
            return Json::success('ok', compact('list', 'tenant_enabled'));
        } catch (\Exception $e) {
            // 如果发生异常，返回错误信息
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function tenantUserList(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $methods           = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function   = $methods[$format] ?? 'formatNormal';
            $systemUserService = Container::make(SystemUserService::class);
            $total             = $systemUserService->getCount($where, [TenantScope::class, AccessScope::class]);
            $list              = $systemUserService->selectList($where, $field, $page, $limit, $order, [], false, [TenantScope::class, AccessScope::class]);
            return call_user_func([$this, $format_function], $list, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

}
