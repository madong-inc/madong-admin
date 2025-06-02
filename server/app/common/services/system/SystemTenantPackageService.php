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

use app\common\dao\system\SystemTenantPackageDao;
use app\common\model\system\SystemTenantPackage;
use madong\basic\BaseService;

use madong\exception\AdminException;
use madong\helper\PropertyCopier;
use support\Container;

class SystemTenantPackageService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemTenantPackageDao::class);
    }

    /**
     * save
     *
     * @param array $data
     *
     * @return SystemRole|null
     */
    public function save(array $data): SystemTenantPackage|null
    {
        try {
            return $this->transaction(function () use ($data) {
                $menus = $data['permissions'] ?? [];
                $model = $this->dao->save($data);
                $model->menus()->sync($menus);
                return $model;
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 编辑
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
                $menus = $data['permissions'] ?? [];
                $model = $this->dao->get($id);
                PropertyCopier::copyProperties((object)$data, $model);
                if (!empty($model)) {
                    $model->save();
                    $model->menus()->sync($menus);
                }
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
