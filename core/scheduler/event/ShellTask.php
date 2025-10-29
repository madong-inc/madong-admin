<?php

namespace core\scheduler\event;

use core\enum\system\OperationResult;

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
