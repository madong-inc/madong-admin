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

namespace app\service\admin\generator;

use app\dao\generator\GeneratorTableDao;
use core\base\BaseService;
use core\exception\handler\AdminException;
use support\Db;

class GeneratorTableService extends BaseService
{
    public function __construct(GeneratorTableDao $dao)
    {
        $this->dao = $dao;
    }

}