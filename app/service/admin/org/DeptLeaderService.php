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

namespace app\service\admin\org;

use app\dao\org\DeptLeaderDao;
use core\base\BaseService;

/**
 * @author Mr.April
 * @since  1.0
 */
class DeptLeaderService extends BaseService
{

    public function __construct(DeptLeaderDao $dao)
    {
        $this->dao = $dao;
    }

}
