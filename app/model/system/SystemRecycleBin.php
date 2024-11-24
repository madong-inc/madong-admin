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

use madong\basic\BaseTpORMModel;

/**
 * 附件模型
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemRecycleBin extends BaseTpORMModel
{

    /**
     * 数据表主键
     *
     * @var string
     */
    protected $pk = 'id';

    protected $name = 'system_recycle_bin';

    public function operate(): \think\model\relation\hasOne
    {
        return $this->hasOne(SystemUser::class, 'id', 'operate_id')->bind(['operate_name' => 'real_name']);
    }
}
