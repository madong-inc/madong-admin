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


class EvalTask implements CronTaskParser
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
            $log = eval($crontab['target']);
        } catch (\Throwable $throwable) {
            $code = 1;
            $log = $throwable->getMessage();
        }
        return ['log'=> $log, 'code' => $code];
    }

}
