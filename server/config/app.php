<?php
/**
 * This file is part of webman.
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use support\Request;
use madong\services\scheduler\event\EvalTask;
use madong\services\scheduler\event\ShellTask;
use madong\services\scheduler\event\UrlTask;
use madong\services\scheduler\event\SchedulingTask;

return [
    'debug'                    => true,
    "lang"                     => 'zh_CN',//默认语言
    'error_reporting'          => E_ALL,
    'default_timezone'         => 'Asia/Shanghai',
    'request_class'            => Request::class,
    'public_path'              => base_path() . DIRECTORY_SEPARATOR . 'public',
    'runtime_path'             => base_path(false) . DIRECTORY_SEPARATOR . 'runtime',
    'controller_suffix'        => 'Controller',
    'controller_reuse'         => false,
    'store_in_recycle_bin'     => true,//是否开启回站模式
    'is_tenant_mode_enabled'   => false,//是否租户模式
    'tenant_enabled'      => true,//是否租户模式
    //枚举扫描配置-待优化实现自动扫描子目录
    //将配置数组中分隔符使用正斜杠 /，PHP 在 Windows 下会自动转换，而 Linux 只识别正斜杠
    'enum_scan_directories'    => [
        app_path('enum'),//app目录
        app_path('common/enum'),//公共目录
        app_path('common/enum/system'),//系统枚举目录
    ],
    //回收站排除的表
    'exclude_from_recycle_bin' => [
        'system_login_log',
        'system_operate_log',
        'system_recycle_bin',
    ],
];
