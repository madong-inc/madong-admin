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

namespace app\common\services\system;

use app\common\dao\system\SystemDeptDao;
use app\common\model\system\SystemDept;
use app\common\scopes\global\AccessScope;
use app\common\scopes\global\TenantScope;
use madong\basic\BaseService;
use madong\exception\AdminException;
use madong\helper\PropertyCopier;
use support\Container;
use think\facade\Db;

class SystemDeptService extends BaseService
{

    public function __construct(SystemDeptDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * save
     *
     * @param array $data
     *
     * @return mixed
     */
    public function save(array $data): mixed
    {
        try {
            return $this->transaction(function () use ($data) {
                $leaders = $data['leader_id_list'] ?? [];//部门领导
                $model   = $this->dao->save($data);
                if (!empty($leaders)) {
                    $model->leader()->sync($leaders);
                }
                return $model;
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * update
     *
     * @param $id
     * @param $data
     *
     * @return void
     */
    public function update($id, $data): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                $leaders = $data['leader_id_list'] ?? [];//部门领导
                $model = $this->dao->get($id,['*'],[],'',[TenantScope::class,AccessScope::class]);
                PropertyCopier::copyProperties((object)$data, $model);
                if (!empty($model)) {
                    unset($model->leader_id_list);
                    $model->save();
                    $model->leader()->sync($leaders);
                }
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * destroy
     *
     * @param $id
     * @param $force
     *
     * @return void
     */
    public function destroy($id, $force): void
    {
        try {
            $this->transaction(function () use ($id, $force) {
                $res = $this->dao->count(['pid' => $id], true);
                if ($res > 0) {
                    throw new AdminException('该部门下存在子部门，请先删除子部门');
                }
                $this->dao->destroy($id);
                $systemDeptLeaderService = Container::make(SystemDeptLeaderService::class);
                $systemDeptLeaderService->dao->delete(['dept_id' => $id]);
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
