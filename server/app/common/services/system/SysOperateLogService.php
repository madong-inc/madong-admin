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

use app\common\dao\system\SysOperateLogDao;
use madong\admin\abstract\BaseService;
use support\Container;

/**
 * @method save(array $data)
 */
class SysOperateLogService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SysOperateLogDao::class);
    }

}
