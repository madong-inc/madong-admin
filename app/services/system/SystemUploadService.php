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

use app\dao\system\SystemUploadDao;
use madong\basic\BaseService;
use support\Container;

class SystemUploadService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemUploadDao::class);
    }


}
