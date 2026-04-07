<?php

namespace core\scheduler\event;

use app\enum\system\OperationResult;
use core\scheduler\event\EventBootstrap;

class ShellTask implements EventBootstrap
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
            $log = shell_exec($crontab['target']);
        } catch (\Throwable $e) {
            $code = OperationResult::FAILURE->value;
            $log  = $e->getMessage();
        }
        return ['code' => $code, 'log' => $log];
    }

}
