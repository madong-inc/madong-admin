<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */

namespace app\model\system;

use think\model\Pivot;

/**
 * 关联模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemRoleMenu extends Pivot
{
    protected $table = 'system_role_menu';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}
