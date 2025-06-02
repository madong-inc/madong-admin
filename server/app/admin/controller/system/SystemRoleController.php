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
use app\admin\validate\system\SystemRoleValidate;
use app\common\services\system\SystemRoleService;
use madong\exception\AdminException;
use madong\utils\Json;
use support\Container;
use support\Request;

/**
 * @author Mr.April
 * @since  1.0
 */
class SystemRoleController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service  = Container::make(SystemRoleService::class);
        $this->validate = Container::make(SystemRoleValidate::class);
    }

    /**
     * 新增角色
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

            if (!isset($data['permissions'])) {
                $data['permissions'] = [];
            }

            $model = $this->service->save($data);
            if (empty($model)) {
                throw new AdminException('插入失败');
            }
            $pk = $model->getPk();
            return Json::success('ok', [$pk => $model->getData($pk)]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新角色
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
            $this->service->update($data['id'], $data);
            return Json::success('ok', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 分配数据权限
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function dataScope(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all(), ['permissions']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('data-scope')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->updateScope($data['id'], $data);
            return Json::success('ok', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }
}
