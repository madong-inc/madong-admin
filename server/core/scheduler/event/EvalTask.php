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
namespace core\scheduler\event;


use app\common\enum\system\OperationResult;

class EvalTask implements EventBootstrap
{
    /**
     * @param $crontab
     *
     * @return array
     */
    public static function parse($crontab): array
    {
        $code = OperationResult::SUCCESS->value;
        try {
            $log = eval($crontab['target']);
        } catch (\Throwable $throwable) {
            $code = OperationResult::FAILURE->value;
            $log = $throwable->getMessage();
        }
        return ['log'=> $log, 'code' => $code];
    }

}
