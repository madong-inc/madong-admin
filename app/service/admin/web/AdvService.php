<?php
declare(strict_types=1);

namespace app\service\admin\web;

use app\dao\web\AdvDao;
use core\base\BaseService;

/**
 * 广告服务类
 */
class AdvService extends BaseService
{
    /**
     * 构造方法
     */
    public function __construct(AdvDao $dao)
    {
        $this->dao = $dao;
    }
}