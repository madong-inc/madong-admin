<?php
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

namespace core\base;

abstract class BaseController
{

    /**
     * @var \core\base\BaseService|null
     */
    protected BaseService|null $service = null;


    /**
     * @var \core\base\BaseValidate|null
     */
    protected BaseValidate|null $validate=null;

    /**
     * 构造方法
     *
     * @access public
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * @return void
     */
    abstract protected function initialize(): void;

}
