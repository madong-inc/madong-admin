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

namespace madong\utils;

class ServerHealthMonitor
{
    /**
     * 获取 CPU 信息
     *
     * @return array
     */
    public function getCpuInfo(): array
    {
        try {
            if (PHP_OS === 'Linux') {
                $cpu = $this->getCpuUsage();
                preg_match('/(\d+)/', shell_exec('cat /proc/cpuinfo | grep "cache size"'), $cache);
                if (count($cache) === 0) {
                    $cache = trim(shell_exec("lscpu | grep L3 | awk '{print \$NF}'") ?? '');
                    if ($cache === '') {
                        $cache = trim(shell_exec("lscpu | grep L2 | awk '{print \$NF}'") ?? '');
                    }
                    if ($cache !== '') {
                        $cache = [0, intval(str_replace(['K', 'B'], '', strtoupper($cache)))];
                    }
                }
            } else {
                $cpu   = shell_exec('wmic cpu get LoadPercentage | findstr /V "LoadPercentage"');
                $cpu   = intval(trim($cpu ?? '0'));
                $cache = shell_exec('wmic cpu get L3CacheSize | findstr /V "L3CacheSize"');
                $cache = trim($cache ?? '');
                if ($cache === '') {
                    $cache = shell_exec('wmic cpu get L2CacheSize | findstr /V "L2CacheSize"');
                    $cache = trim($cache ?? '');
                }
                if ($cache !== '') {
                    $cache = [0, intval($cache) * 1024];
                }
            }
            return [
                'name'  => $this->getCpuName(),
                'cores' => '物理核心数：' . $this->getCpuPhysicsCores() . '个，逻辑核心数：' . $this->getCpuLogicCores() . '个',
                'cache' => $cache[1] ? $cache[1] / 1024 : 0,
                'usage' => $cpu,
                'free'  => round(100 - $cpu, 2),
            ];
        } catch (\Throwable $e) {
            $res = '无法获取';
            echo $e->getMessage(), "\n";
            return [
                'name'  => $res,
                'cores' => $res,
                'cache' => $res,
                'usage' => $res,
                'free'  => $res,
            ];
        }
    }

    /**
     * 获取 CPU 名称
     *
     * @return string
     */
    public function getCpuName(): string
    {
        if (PHP_OS === 'Linux') {
            preg_match('/^\s+\d\s+(.+)/', shell_exec('cat /proc/cpuinfo | grep name | cut -f2 -d: | uniq -c') ?? '', $matches);
            if (count($matches) === 0) {
                $name = trim(shell_exec("lscpu| grep Architecture | awk '{print $2}'") ?? '');
                if ($name !== '') {
                    $mfMhz = trim(shell_exec("lscpu| grep 'MHz' | awk '{print \$NF}' | head -n1") ?? '');
                    $mfGhz = trim(shell_exec("lscpu| grep 'GHz' | awk '{print \$NF}' | head -n1") ?? '');
                    if ($mfMhz === '' && $mfGhz === '') {
                        return $name;
                    } elseif ($mfGhz !== '') {
                        return $name . ' @ ' . $mfGhz . 'GHz';
                    } elseif ($mfMhz !== '') {
                        return $name . ' @ ' . round(intval($mfMhz) / 1000, 2) . 'GHz';
                    }
                } else {
                    return '未知';
                }
            }
            return $matches[1] ?? "未知";
        } else {
            $name = shell_exec('wmic cpu get Name | findstr /V "Name"');
            return trim($name);
        }
    }

    /**
     * 获取 CPU 物理核心数
     *
     * @return string
     */
    public function getCpuPhysicsCores(): string
    {
        if (PHP_OS === 'Linux') {
            $num = str_replace("\n", '', shell_exec('cat /proc/cpuinfo | grep "physical id" | sort | uniq | wc -l'));
            return intval($num) === 0 ? '1' : $num;
        } else {
            $num  = shell_exec('wmic cpu get NumberOfCores | findstr /V "NumberOfCores"');
            $num  = trim($num ?? '1');
            $nums = explode("\n", $num);
            $num  = 0;
            foreach ($nums as $n) {
                $num += intval(trim($n));
            }
            return strval($num);
        }
    }

    /**
     * 获取 CPU 逻辑核心数
     *
     * @return string
     */
    public function getCpuLogicCores(): string
    {
        if (PHP_OS === 'Linux') {
            return str_replace("\n", '', shell_exec('cat /proc/cpuinfo | grep "processor" | wc -l'));
        } else {
            $num  = shell_exec('wmic cpu get NumberOfLogicalProcessors | findstr /V "NumberOfLogicalProcessors"');
            $num  = trim($num ?? '1');
            $nums = explode("\n", $num);
            $num  = 0;
            foreach ($nums as $n) {
                $num += intval(trim($n));
            }
            return strval($num);
        }
    }

    /**
     * 获取 CPU 使用率
     *
     * @return string
     */
    public function getCpuUsage(): string
    {
        $start = $this->calculationCpu();
        sleep(1);
        $end = $this->calculationCpu();

        $totalStart = $start['total'];
        $totalEnd   = $end['total'];

        $timeStart = $start['time'];
        $timeEnd   = $end['time'];

        return sprintf('%.2f', ($timeEnd - $timeStart) / ($totalEnd - $totalStart) * 100);
    }

    /**
     * 计算 CPU
     *
     * @return array
     */
    protected function calculationCpu(): array
    {
        $mode   = '/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/';
        $string = shell_exec('cat /proc/stat | grep cpu');
        preg_match_all($mode, $string, $matches);

        $total = array_sum(array_slice($matches[2], 0, 8));
        $time  = $matches[2][0] + $matches[3][0] + $matches[4][0] + $matches[6][0] + $matches[7][0] + $matches[8][0] + $matches[9][0];

        return ['total' => $total, 'time' => $time];
    }

    /**
     * 获取内存信息
     *
     * @return array
     */
    public function getMemInfo(): array
    {
        if (PHP_OS === 'Linux') {
            $total     = shell_exec('grep MemTotal /proc/meminfo | awk \'{print $2}\'');
            $available = shell_exec('grep MemAvailable /proc/meminfo | awk \'{print $2}\'');

            $result['total'] = sprintf('%.2f', $total / 1024 / 1024);
            $result['free']  = sprintf('%.2f', $available / 1024 / 1024);
            $result['usage'] = sprintf('%.2f', ($total - $available) / 1024 / 1024);
            $result['php']   = round(memory_get_usage() / 1024 / 1024, 2);
            $result['rate']  = sprintf('%.2f', ($result['usage'] / $result['total']) * 100);
        } else {
            $cap             = shell_exec('wmic Path Win32_PhysicalMemory Get Capacity | findstr /V "Capacity"');
            $cap             = trim($cap ?? '');
            $total           = array_sum(array_map('intval', explode("\n", $cap)));
            $result['total'] = round($total / 1024 / 1024 / 1024, 2);

            $free            = shell_exec('wmic OS get FreePhysicalMemory | findstr /V "FreePhysicalMemory"');
            $result['free']  = round(intval($free) / 1024 / 1024, 2);
            $result['usage'] = round($result['total'] - $result['free'], 2);
            $result['php']   = round(memory_get_usage() / 1024 / 1024, 2);
            $result['rate']  = sprintf('%.2f', ($result['usage'] / $result['total']) * 100);
        }

        return $result;
    }

    /**
     * 获取 PHP 及环境信息
     *
     * @return array
     */
    public function getPhpAndEnvInfo(): array
    {
        return [
            'php_version'  => PHP_VERSION,
            'os'           => PHP_OS,
            'project_path' => BASE_PATH,
        ];
    }
}