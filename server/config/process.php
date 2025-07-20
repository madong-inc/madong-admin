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

use app\common\process\Http;
use support\Log;
use support\Request;

global $argv;

return [
    'webman'            => [
        'handler'     => Http::class,
        'listen'      => 'http://0.0.0.0:8899',
        'count'       => cpu_count() * 4,
        'user'        => '',
        'group'       => '',
        'reusePort'   => false,
        'eventLoop'   => '',
        'context'     => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger'       => Log::channel('default'),
            'appPath'      => app_path(),
            'publicPath'   => public_path(),
        ],
    ],
    'monitor'           => [
        'handler'     => \app\common\process\Monitor::class,
        'reloadable'  => false,
        // File update detection and automatic reload
        'constructor' => [
            // Monitor these directories
            'monitorDir'        => array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                //                base_path() . '/.env',//这里注释避免安装过程中失联
                base_path() . '/core',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env',
            ],
            'options'           => [
                'enable_file_monitor'   => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ],
        ],
    ],
    'webman-scheduler'  => [
        'handler' => \core\scheduler\SchedulerServer::class,
        'count'   => 1,
        'listen'  => 'text://' . config('core.scheduler.app.listen', '0.0.0.0:2346'),
    ],
    'push_notification' => [
        'handler' => \app\common\process\PushNotification::class,
    ],
];
