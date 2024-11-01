<?php

namespace madong\services\scheduler;

class ShellTask implements CronTaskParser
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
