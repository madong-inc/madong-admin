<?php
declare(strict_types=1);

namespace app\service\admin\web;

use app\dao\web\LinkDao;
use core\base\BaseService;

/**
 * 友情链接服务类
 */
class LinkService extends BaseService
{
    /**
     * 构造方法
     */
    public function __construct(LinkDao $dao)
    {
        $this->dao = $dao;
    }
}
