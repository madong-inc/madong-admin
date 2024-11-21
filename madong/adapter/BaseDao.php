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

namespace madong\adapter;

use BadMethodCallException;

/**
 * @method getModel()
 */
abstract class BaseDao
{

    /**
     * 获取当前模型
     *
     * @return string
     */
    abstract protected function setModel(): string;

    protected mixed $instance;

    public function __construct()
    {
        $mode           = config('madong.model_type', 'thinkORM');
        $modelClass     = $this->setModel();
        $this->instance = Factory::create($mode, $modelClass);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->instance, $name)) {
            return $this->instance->$name(...$arguments);
        }
        throw new BadMethodCallException("Method $name does not exist");
    }
}
