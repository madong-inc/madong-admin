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

namespace app\admin\controller\monitor;

use app\admin\controller\Base;
use app\admin\controller\Crud;
use madong\services\monitor\ServerMonitor;
use madong\utils\Json;
use support\Container;
use support\Request;
use support\Response;

/**
 * 性能监控
 *
 * @author Mr.April
 * @since  1.0
 */
class ServerController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
    }


    /**
     * 性能监控
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        try {
            $service = Container::make(ServerMonitor::class);
            $serverInfo = [
                'cpu'    => $service->getCpuInfo(),
                'memory' => $service->getMemoryInfo(),
                'disk'   => $service->getDiskInfo(),
                'php'    => $service->getPhpInfo(),
            ];

            return Json::success('ok', $serverInfo);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    private function getMemoryInfo(): array
    {
        // 获取内存信息
        $memory = [];
        if (function_exists('memory_get_usage')) {
            $memory['usage']      = memory_get_usage();
            $memory['peak_usage'] = memory_get_peak_usage();
        }

        // 获取系统内存信息
        if (stristr(PHP_OS, 'WIN')) {
            // Windows 系统
            $memoryInfo = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize');
            $memoryInfo = preg_split('/\s+/', trim($memoryInfo));

            // 处理输出，跳过标题行
            $memory['total'] = isset($memoryInfo[1]) ? $memoryInfo[1] / 1024 : 0; // 转换为 MB
            $memory['free']  = isset($memoryInfo[0]) ? $memoryInfo[0] / 1024 : 0;  // 转换为 MB
        } else {
            // Linux 系统
            $memoryInfo = shell_exec('free -m');
            $lines      = explode("\n", trim($memoryInfo));
            $memoryData = preg_split('/\s+/', $lines[1]); // 取第二行

            $memory['total'] = isset($memoryData[1]) ? $memoryData[1] : 0; // 总内存
            $memory['free']  = isset($memoryData[3]) ? $memoryData[3] : 0;  // 可用内存
        }

        return $memory;
    }

    private function getUptime(): string
    {
        // 获取系统运行时间
        $uptime = shell_exec('uptime -p');
        return trim($uptime);
    }

}
