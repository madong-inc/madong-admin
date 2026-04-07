<?php
declare(strict_types=1);
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

namespace app\service\admin\notice;

use app\dao\notice\NoticeDao;
use app\model\notice\Notice;
use core\base\BaseService;
use core\exception\handler\AdminException;
use core\uuid\UUIDGenerator;

class NoticeService extends BaseService
{

    public function __construct(NoticeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 保存
     *
     * @param array $data
     *
     * @return \app\model\notice\Notice|null
     * @throws \core\exception\handler\AdminException
     */
    public function save(array $data): ?Notice
    {
        try {
            return $this->transaction(function () use ($data) {
                $data['uuid'] = UUIDGenerator::generate();
                return $this->dao->save($data);
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param string|int $id
     * @param array      $data
     *
     * @throws \core\exception\handler\AdminException
     */
    public function update(string|int $id, array $data): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                $data['uuid'] = UUIDGenerator::generate();//更新之后变更uuid 再次推送重新发起
                $model        = $this->dao->get($id);
                $model->fill($data);
                $model->save();
            });
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

}
