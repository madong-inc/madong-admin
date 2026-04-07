<?php
declare(strict_types=1);

namespace app\dao\web;

use app\model\web\Link;
use core\base\BaseDao;

/**
 * 友情链接数据访问对象
 */
class LinkDao extends BaseDao
{
    /**
     * 设置模型类
     */
    protected function setModel(): string
    {
        return Link::class;
    }
}
