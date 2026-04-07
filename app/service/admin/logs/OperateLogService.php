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

namespace app\service\admin\logs;

use app\dao\logs\OperateLogDao;
use core\base\BaseService;

/**
 * @method save(array $data)
 */
class OperateLogService extends BaseService
{

    public function __construct(OperateLogDao $dao)
    {
        $this->dao = $dao;
    }

}
