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

use app\common\dao\system\SystemDictItemDao;
use core\abstract\BaseService;
use support\Container;

/**
 * 数据字段服务
 *
 * @author Mr.April
 * @since  1.0
 */
class SysDictItemService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemDictItemDao::class);
    }

}
