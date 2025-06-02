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

namespace app\common\process;

use Webman\Push\Api;

class PushNotification
{

    public function onWorkerStart(): void
    {

        $api = new Api(
//            'http://127.0.0.1:3232',
            config('plugin.webman.push.app.api'),
            config('plugin.webman.push.app.app_key'),
            config('plugin.webman.push.app.app_secret')
        );
        // 给订阅 admin 的所有客户端推送 message 事件的消息
        $return_ret = [
            'event'   => 'message',
            'message' => '新消息通知',
            'data'    => [
                [
                    'id'        => 1,
                    'uid'       => 2,
                    'avatar'    => '',
                    'is_read'   => false,
                    'title'     => '系统消息',
                    'message'   => '欢迎使用MadongPRO框架',
                    'date'      => date('Y-m-d H:i:s'),
                    'send_user' => [
                        'nickname' => '系统管理员',
                        'avatar'   => '',
                    ],
                ],
            ],
        ];
        $api->trigger('admin', 'message', $return_ret);
    }
}
