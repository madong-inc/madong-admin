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
namespace madong\exception;

use Throwable;

/**
 * 自定义异常类
 *
 * @package plugin\saiadmin\app\exception
 */
class UploadException extends \RuntimeException
{
    public function __construct($message, $code = -1, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
