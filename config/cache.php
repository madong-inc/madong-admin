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

return [
    'default' => 'file',
    'stores'  => [
        'file'  => [
            'driver' => 'file',
            'path'   => runtime_path('cache'),
        ],
        'redis' => [
            'driver'     => 'redis',
            'connection' => 'default',
        ],
    ],
    //对应自定义扩展
    'custom'  => [
        'default'   => [
            'host'     => '127.0.0.1',
            'password' => null,
            'port'     => 6379,
            'database' => 0,
        ],
        'namespace' => '',
        'prefix'    => 'ma_dong',
        'type'      => 'redis',//redis||file
        'stores' => [
            'redis' => [
                'host'     => '127.0.0.1',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
