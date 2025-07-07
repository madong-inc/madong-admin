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

namespace app\admin\controller\platform;

use app\admin\controller\Crud;
use app\admin\validate\platform\DbSettingValidate;
use app\common\services\platform\DbSettingService;
use madong\admin\utils\Json;
use madong\admin\utils\Util;
use support\Container;
use support\Request;

/**
 * @author Mr.April
 * @since  1.0
 */
class DbSettingController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(DbSettingService::class);
        $this->validate = Container::make(DbSettingValidate::class);
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

            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->save($data);
            Util::reloadWebman();
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 删除
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function destroy(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id');
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('destroy')->check(['id' => $id])) {
                    throw new \Exception($this->validate->getError());
                }
            }
            //第二参数是否删除实体表
            $this->service->remove($id,true);
            Util::reloadWebman();
            return Json::success('ok', []);
        } catch (\Exception $e) {
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
            $is_tenant_mode_enabled = config('app.is_tenant_mode_enabled', false);

            if (!$is_tenant_mode_enabled) {
                // 如果未启用租户模式，直接返回空数组和相关配置
                $list = [];
                return Json::success('ok', compact('list', 'is_tenant_mode_enabled'));
            }

            // 如果启用了租户模式，执行数据库查询
            $list = $this->service->selectList(['enabled' => 1], '*', 0, 0, '', [], false)
                ->setVisible(['id', 'tenant_id', 'name'])
                ->toArray();
            return Json::success('ok', compact('list', 'is_tenant_mode_enabled'));
        } catch (\Exception $e) {
            // 如果发生异常，返回错误信息
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 格式化下拉列表
     *
     * @param $items
     *
     * @return \support\Response
     */
    protected function formatSelect($items): \support\Response
    {
        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'label' => $item->name,
                'value' => $item->database,
            ];
        }
        return Json::success('ok', $formatted_items);
    }
}
