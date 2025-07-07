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

return [
    'debug'                    => env('APP_DEBUG', false),
    "lang"                     => 'zh_CN',//默认语言
    'error_reporting'          => E_ALL,
    'default_timezone'         => 'Asia/Shanghai',
    'request_class'            => Request::class,
    'public_path'              => base_path() . DIRECTORY_SEPARATOR . 'public',
    'runtime_path'             => base_path(false) . DIRECTORY_SEPARATOR . 'runtime',
    'controller_suffix'        => 'Controller',
    'controller_reuse'         => false,
    'store_in_recycle_bin'     => env('RECYCLE_BIN_ENABLED', false),//是否开启回站模式
    //枚举扫描配置-待优化实现自动扫描子目录
    //将配置数组中分隔符使用正斜杠 /，PHP 在 Windows 下会自动转换，而 Linux 只识别正斜杠
    'enum_scan_directories'    => [
        app_path('enum'),//app目录
        app_path('common/enum'),//公共目录
        app_path('common/enum/system'),//系统枚举目录
        app_path('packages/admin/src/enum/system'),//系统枚举目录
        app_path('common/enum/platform'),//平台枚举目录
        base_path('vendor/madong/ingenious/src/enums'),//目录枚举
    ],
    //回收站排除的表
    'exclude_from_recycle_bin' => [
        'system_login_log',
        'system_operate_log',
        'system_recycle_bin',
        'wf_process_cc_instance',
        'wf_process_define',
        'wf_process_define_favorite',
        'wf_process_design',
        'wf_process_design_history',
        'wf_process_form',
        'wf_process_form_history',
        'wf_process_instance',
        'wf_process_instance_history',
        'wf_process_surrogate',
        'wf_process_task',
        'wf_process_task_actor',
        'wf_process_task_actor_history',
        'wf_process_task_history',
        'wf_process_type',
    ],
];
