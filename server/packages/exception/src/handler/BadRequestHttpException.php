<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitcode.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace madong\exception\handler;


class BadRequestHttpException extends BaseException
{
    /**
     * @var int
     */
    public int $statusCode = 400;

    /**
     * @var string
     */
    public string $errorMessage = '请求参数错误，请检查后重试';
}
