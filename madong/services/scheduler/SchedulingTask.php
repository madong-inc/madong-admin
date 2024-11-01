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

class SchedulingTask implements CronTaskParser
{
    /**
     * @param $crontab
     *
     * @return array
     */
    public static function parse($crontab): array
    {
        $code = 0;
        try {
            $className = $crontab['target'];
            $bizId     = $crontab['biz_id'];
            // 创建类的实例
            $instance = new $className();
            // 使用反射动态调用非静态方法
            $reflectionMethod = new \ReflectionMethod($className, 'run');
            $log              = $reflectionMethod->invokeArgs($instance, [$bizId]);
        } catch (\Throwable $throwable) {
            $code = 1;
            $log  = $throwable->getMessage();
        }
        return ['log' => $log, 'code' => $code];
    }

}
