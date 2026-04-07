<?php
declare(strict_types=1);

namespace app\service\admin\web;

use app\dao\web\MenuDao;
use core\base\BaseService;

/**
 * 菜单服务类
 */
class MenuService extends BaseService
{
    /**
     * 构造方法
     */
    public function __construct(MenuDao $dao)
    {
        $this->dao = $dao;
    }
}
