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

use app\common\dao\system\SystemRoleScopeDeptDao;
use madong\basic\BaseService;
use support\Container;

/**
 * @method getColumn(int[]|string[] $array, string $string)
 */
class SystemRoleScopeDeptService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemRoleScopeDeptDao::class);
    }

}
