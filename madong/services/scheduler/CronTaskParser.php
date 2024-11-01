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
namespace madong\services\scheduler;

/**
 * 解析任务
 *
 * @author Mr.April
 * @since  1.0
 */
interface CronTaskParser
{
    public static function parse($crontab): mixed;
}
