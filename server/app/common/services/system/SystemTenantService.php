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

use app\common\dao\system\SystemTenantDao;
use app\common\model\system\SystemTenant;
use madong\basic\BaseService;

use madong\exception\AdminException;
use madong\helper\PropertyCopier;
use madong\services\uuid\UUIDGenerator;
use support\Container;

class SystemTenantService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemTenantDao::class);
    }

    /**
     * 保存
     *
     * @param array $data
     *
     * @return SystemRole|null
     */
    public function save(array $data): SystemTenant|null
    {
        try {
            return $this->transaction(function () use ($data) {
                $data['tenant_id']    = UUIDGenerator::generate('custom', 6);//自动生成uuid
                $user['password']     = password_hash($data['password'], PASSWORD_DEFAULT);
                $user['user_name']    = $data['account'] ?? '';
                $user['real_name']    = $data['contact_user_name'] ?? '';
                $user['mobile_phone'] = $data['contact_phone'] ?? '';
                $user['tenant_id']    = $data['tenant_id'];
                $systemUserService    = Container::make(SystemUserService::class);
                $systemUserService->dao->save($user);
                ['expired_date' => $expired] = $data;
                // 解析字符串日期为时间戳
                if (!empty($expired) && is_string($expired)) {
                    $expired = strtotime($expired);
                    if ($expired === false) {
                        throw new \InvalidArgumentException('Invalid date format for expired_at');
                    }
                    $data['expired_at'] = $expired;
                }

                unset($data['password'], $data['account'],$data['expired_date']);
                return $this->dao->save($data);
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
                ['expired_date' => $expired] = $data;
                // 解析字符串日期为时间戳
                if (!empty($expired) && is_string($expired)) {
                    $expired = strtotime($expired);
                    if ($expired === false) {
                        throw new \InvalidArgumentException('Invalid date format for expired_at');
                    }
                    $data['expired_at'] = $expired;
                }

                unset($data['expired_date']);
                $model = $this->dao->get($id);
                PropertyCopier::copyProperties((object)$data, $model);
                $model->save();
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
