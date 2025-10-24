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
use app\admin\validate\dev\RateLimiterValidate;
use app\common\services\system\SysRetaLimiterService;
use core\exception\handler\AdminException;
use core\utils\Json;
use support\Container;
use support\Request;

/**
 * 限访规则
 *
 * @author Mr.April
 * @since  1.0
 */
class RateLimiterController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
        $this->service  = Container::make(SysRetaLimiterService::class);
        $this->validate = Container::make(RateLimiterValidate::class);
    }

    /**
     * 保存
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function store(Request $request): \support\Response
    {
        try {
            $data = $this->insertInput($request);
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
            //添加后移除缓存
            $this->service->cacheDriver()->delete(SysRetaLimiterService::CACHE_KEY);
            return Json::success('ok', [$pk => $model->getData($pk)]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新状态
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function changeStatus(Request $request): \support\Response
    {
        try {
            $data       = $this->insertInput($request);
            $model      = $this->service->getModel();
            $primaryKey = $model->getKeyName();
            if (!array_key_exists($primaryKey, $data)) {
                throw new \Exception('参数异常缺少主键');
            }
            $targetModel = $model->findOrFail($data[$primaryKey]);
            if (empty($targetModel)) {
                throw new \Exception('资源不存在' . $primaryKey . '=', $data[$primaryKey]);
            }
            $targetModel->fill($data);
            if (!$targetModel->save()) {
                throw new \RuntimeException('数据保存失败');
            }
            //清空缓存
            $this->service->cacheDriver()->delete(SysRetaLimiterService::CACHE_KEY);
            return Json::success('ok', []);
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
            $data = $this->insertInput($request);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('update')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $this->service->update($data['id'], $data);
            //清空缓存
            $this->service->cacheDriver()->delete(SysRetaLimiterService::CACHE_KEY);
            return Json::success('ok', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function destroy(Request $request): \support\Response
    {
        try {
            $data= $this->getDeleteIds($request);
            if (empty($data)) {
                throw new AdminException('参数错误');
            }
            $result = $this->service->transaction(function () use ($data) {
                $data       = is_array($data) ? $data : explode(',', $data);
                $deletedIds = [];
                foreach ($data as $id) {
                    $item = $this->service->get($id);
                    if (!$item) {
                        continue; // 如果找不到项，跳过
                    }
                    $item->delete();
                    $primaryKey   = $item->getPk();
                    $deletedIds[] = $item->{$primaryKey};
                }
                return $deletedIds;
            });
            //清空缓存
            $this->service->cacheDriver()->delete(SysRetaLimiterService::CACHE_KEY);
            return Json::success('ok', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

}
