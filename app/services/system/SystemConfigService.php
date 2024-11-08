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

use app\dao\system\SystemConfigDao;
use madong\basic\BaseService;
use support\Container;

class SystemConfigService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemConfigDao::class);
    }

    /**
     * 获取配置
     *
     * @param string|int $code
     * @param string|int $groupCode
     *
     * @return mixed
     */
    public function getConfig(string|int $code, string|int $groupCode = ''): mixed
    {
        $map = ['code' => $code, 'group_code' => $groupCode];
        return $this->dao->value($map, 'content');
    }

}
