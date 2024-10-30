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
namespace madong\services\quartz;

use plugin\wf\app\model\CrontabLog;


class Test
{

    /**
     * 定时清理日志
     * \plugin\wf\app\common\Test::clearLog(7);
     * @param $dayNumber  清理N天前的日志
     * @return void
     */
    static function clearLog($dayNumber = 7){
        $rootPart = base_path().'/runtime/logs/';
        $logtypes = ['debug','err','sql','webman'];// 需要清理的日志类型
        $timer = strtotime(date('Y-m-d')) - $dayNumber * 86400;
        $number = 0;
        foreach ($logtypes as $logtype){
            $dir = $rootPart.$logtype;
            if(!is_dir($dir)) continue;
            $files = scandir($dir);
            foreach ($files as $file){
                if($file!='.' && $file!='..'){
                    $date = self::getSubstr($file,$logtype.'-','.log');
                    if($date && strtotime($date)<$timer){
                        // 满足条件 删除记录
                        unlink($dir."/{$file}");
                        $number++;
                    }
                }
            }
        }
        return "本次清理{$number}个日志文件";
    }

    /**
     * 定时清理 定时任务产生的日志记录
     * \plugin\tuCrontabs\app\common\Test::clearCrontabLog(3);
     * @param $dayNumber
     * @return void
     */
    static function clearCrontabLog($dayNumber = 3){
        $time = time() - $dayNumber*86400;
        $number = CrontabLog::where([['create_time', '<', $time]])->delete();
        return "成功清理{$number}条日志";
    }

    static function demo(){
        return 'demo1 调用案例';
    }

    static function demo2(){
        sleep(10);
        return 'demo2 延迟10秒后调用';
    }

    static function getSubstr($str, $leftStr, $rightStr)
    {
        $left = strpos($str, $leftStr);
        if ($left === false) return '';
        $right = strpos($str, $rightStr, $left + strlen($leftStr));
        if ($right === false) return '';
        if ($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
    }

}
