<?php
declare(strict_types=1);

namespace app\dao\web;

use app\model\web\Menu;
use core\base\BaseDao;

/**
 * 菜单数据访问对象
 */
class MenuDao extends BaseDao
{
    /**
     * 设置模型类
     */
    protected function setModel(): string
    {
        return Menu::class;
    }
}
