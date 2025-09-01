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

namespace app\admin\controller\dev;

use app\admin\controller\Crud;
use app\admin\validate\system\SysCrontabValidate;
use app\common\services\system\SysCrontabService;
use core\enum\system\OperationResult;
use core\exception\handler\AdminException;
use core\utils\Json;
use support\Container;
use support\Request;

/**
 * 定时任务
 *
 * @author Mr.April
 * @since  1.0
 */
class CrontabController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
        /** @var  SysCrontabService $service */
        $service        = Container::make(SysCrontabService::class);
        $this->service  = $service;
        $this->validate = Container::make(SysCrontabValidate::class);
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
            $data = $this->inputFilter($request->all(), ['minute', 'hour', 'day', 'week', 'month', 'second']);

            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            /** @var SysCrontabService $service */
            $service = $this->service;
            $model   = $service->save($data);
            if (empty($model)) {
                throw new AdminException('插入失败');
            }
            $pk = $model->getPk();
            return Json::success('ok', [$pk => $model->getData($pk)]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function destroy(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id');
            $data = $request->input('data', []);
            $data = !empty($id) && $id !== '0' ? $id : $data;
            if (empty($data)) {
                throw new AdminException('参数错误');
            }
            $this->service->destroy($data);
            return Json::success('操作成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
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
            $data = $this->inputFilter($request->all(), ['minute', 'hour', 'day', 'week', 'month', 'second']);
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
     * 恢复任务
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function resume(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all(), ['data']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('resume')->check($data)) {
                    throw new \Exception($this->validate->getError(), -1);
                }
            }
            $this->service->resumeCrontab($data['data']);
            return Json::success('执行完成');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 暂停任务
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function pause(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all(), ['data']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('pause')->check($data)) {
                    throw new \Exception($this->validate->getError(), -1);
                }
            }
            $this->service->pauseCrontab($data['data']);
            return Json::success('执行完成');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 执行某个任务
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function execute(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all(), ['data']);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('execute')->check($data)) {
                    throw new \Exception($this->validate->getError(), -1);
                }
            }
            $result = $this->service->runOneTask(['id' => $data['data']]);

            if ($result['code'] == OperationResult::FAILURE->value) {
                throw new AdminException('执行失败' . $result['log']);
            }
            return Json::success('执行完成', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }
}
