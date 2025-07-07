<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */


use support\view\ThinkPHP;


return [
    'handler' => ThinkPHP::class,
//    'options' => [
//        'view_suffix' => 'html',
//        'tpl_cache'   => true,
//        'tpl_begin'   => '{',
//        'tpl_end'     => '}',
//        //'taglib_pre_load' => app\common\Thinktab::class,
//        'tpl_replace_string' => [
//            '{PUBLIC__PATH}' => VT_DIR,
//            '{STATIC__PATH}' => VT_STATIC,
//        ],
//    ]
];