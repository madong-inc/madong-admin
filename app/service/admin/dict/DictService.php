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

namespace app\service\admin\dict;

use app\dao\dict\DictDao;
use core\base\BaseService;

/**
 * 数据字段服务
 *
 * @author Mr.April
 * @since  1.0
 * @method update($where, $data)
 * @method findItemsByCode($dictType)
 */
class DictService extends BaseService
{

    public function __construct(DictDao $dao)
    {
        $this->dao = $dao;
    }

}
