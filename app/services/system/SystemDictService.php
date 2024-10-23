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

namespace app\services\system;

use app\dao\system\SystemDictDao;
use app\model\system\SystemDictItem;
use madong\basic\BaseService;
use support\Container;

/**
 * 数据字段服务
 *
 * @author Mr.April
 * @since  1.0
 * @method update($where, $data)
 */
class SystemDictService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemDictDao::class);
    }

    /**
     * updated
     *
     * @param $id
     * @param $data
     *
     * @return bool
     */
    public function updated($id, $data): bool
    {
        $this->update($id, $data);
        $systemDictItemService = Container::make(SystemDictItemService::class);
        $systemDictItemService->update(['dict_id' => $id], ['code' => $data['code']]);
        return true;
    }

    /**
     * 数据删除
     *
     * @param string|array $ids
     * @param bool         $force
     *
     * @return mixed
     */
    public function destroy(string|array $ids, bool $force = false): mixed
    {
        $result = $this->dao->destroy($ids, $force);
        if ($force) {
            $systemDictItemService = Container::make(SystemDictItemService::class);
            $systemDictItemService->delete([['dict_id', 'in', $ids]]);
        }
        return $result ?? [];
    }
}
