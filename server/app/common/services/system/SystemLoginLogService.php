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

use app\common\dao\system\SystemLoginLogDao;
use madong\basic\BaseService;
use support\Container;

/**
 * @method save(array $data)
 */
class SystemLoginLogService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemLoginLogDao::class);
    }

}
