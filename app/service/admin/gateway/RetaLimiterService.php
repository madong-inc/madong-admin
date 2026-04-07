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

namespace app\service\admin\gateway;

use app\dao\gateway\RateLimiterDao;
use core\base\BaseService;

class RetaLimiterService extends BaseService
{

    public function __construct(RateLimiterDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 缓存key
     */
    const CACHE_KEY = 'rate_limiter';
}
