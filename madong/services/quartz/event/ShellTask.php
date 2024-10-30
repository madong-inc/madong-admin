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

class ShellTask implements EventBootstrap
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
            $log = shell_exec($crontab['target']);
        } catch (\Throwable $e) {
            $code = 1;
            $log = $e->getMessage();
        }
        return ['code' => $code, 'log' => $log];
    }

}
