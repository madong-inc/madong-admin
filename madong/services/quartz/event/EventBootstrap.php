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
namespace madong\services\quartz\event;

interface EventBootstrap
{
    /**
     * @param $crontab
     * @return mixed
     * 解析任务
     */
    public static function parse($crontab): mixed;
}
