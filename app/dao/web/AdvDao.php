<?php
declare(strict_types=1);

namespace app\dao\web;

use app\model\web\Adv;
use core\base\BaseDao;

/**
 * 广告数据访问对象
 */
class AdvDao extends BaseDao
{
    /**
     * 设置模型类
     */
    protected function setModel(): string
    {
        return Adv::class;
    }
}