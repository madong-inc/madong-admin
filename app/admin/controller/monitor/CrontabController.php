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

namespace app\admin\controller\monitor;

use app\admin\controller\Crud;
use app\admin\validate\system\SystemCrontabValidate;
use app\services\system\SystemCrontabLogService;
use app\services\system\SystemCrontabService;
use madong\exception\AdminException;
use madong\utils\Json;
use support\Container;
use support\Request;

/**
 *
 * 定时任务
 * @author Mr.April
 * @since  1.0
 */
class CrontabController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
        $this->service  = Container::make(SystemCrontabService::class);
        $this->validate = Container::make(SystemCrontabValidate::class);
    }

    /**
     * 启动任务
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function start(Request $request): \support\Response
    {
        try {
            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('start')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $result = $this->service->runOneTask($data['id']);
            if ($result['code'] == 1) {
                throw new AdminException('执行失败' . $result['log']);
            }
            return Json::success('执行完成', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
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
            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('resume')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($data['id'], ['enabled' => 1]);//更改启用
            $result = $this->service->requestData($data['id']);
            if (!$result) {
                throw new AdminException('恢复失败');
            }
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
            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('pause')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($data['id'], ['enabled' => 0]);//更改禁用
            $result = $this->service->requestData($data['id']);
            if (!$result) {
                throw new AdminException('重启失败');
            }
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
            $data = $this->inputFilter($request->all());
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('execute')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $result = $this->service->runOneTask($data['id']);
            if ($result['code'] == 1) {
                throw new AdminException('执行失败' . $result['log']);
            }
            return Json::success('执行完成', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 定时任务删除
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function destroy(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id'); // 获取路由地址 id从
            $data = $request->input('data', []);
            $id   = !empty($id) && $id !== '0' ? $id : $data;
            if (empty($id)) {
                throw new AdminException('参数错误');
            }
            //1.0 先关闭再删除,避免删了后直接连不上服务的情况出现
            $this->service->update($id, ['enabled' => 0]);
            //2.0 重启任务
            $this->service->requestData($id);

            //3.0 删除定时任务跟日志数据
            $this->service->destroy($id);
            $systemCrontabLogService = Container::make(SystemCrontabLogService::class);
            $systemCrontabLogService->delete(['crontab_id' => $id]);
            return Json::success('删除成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

}
