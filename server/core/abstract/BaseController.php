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

namespace core\abstract;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     *
     * @var \support\Request
     */
    protected \support\Request $request;

    /**
     * 控制器中间件
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * @var \madong\admin\basic\BaseService|null
     */
    protected ?BaseService $service;

    /**
     * @var object|null
     */
    protected ?object $validate;

    /**
     * 需要授权的接口地址
     *
     * @var string[]
     */
    private array $authRule = [];

    /**
     * 构造方法
     *
     * @access public
     */
    public function __construct()
    {
        $this->request = request();
        $this->initialize();
    }

    /**
     * @return void
     */
    abstract protected function initialize(): void;

}
