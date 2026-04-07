<?php
declare(strict_types=1);

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

namespace app\adminapi\listener;

use app\adminapi\event\LoginLogEvent;
use app\dao\logs\LoginLogDao;
use support\Container;

/**
 * 登录日志监听器
 * 处理登录日志事件，将登录记录保存到数据库
 */
class LoginLogListener
{
    /**
     * 处理登录日志事件
     *
     * @param LoginLogEvent $event
     *
     * @return void
     * @throws \Exception
     */
    public function handle(LoginLogEvent $event): void
    {
        // 保存登录日志到数据库
        $this->saveLoginLog($event);
    }

    /**
     * 保存登录日志
     *
     * @param LoginLogEvent $event
     *
     * @return void
     * @throws \Exception
     */
    private function saveLoginLog(LoginLogEvent $event): void
    {
        /** @var LoginLogDao $dao */
        $dao = Container::make(LoginLogDao::class);

        $dao->save([
            'user_id'     => $event->userId,
            'ip'          => $event->ip,
            'ip_location' => $event->ipLocation,
            'os'          => $event->os,
            'browser'     => $event->browser,
            'status'      => $event->status,
            'message'     => $event->message,
            'login_time'  => $event->loginTime,
            'key'         => md5($event->accessToken),
            'expires_at'  => $event->expiresAt,
            'remark'      => $event->status == 1 ? '登录成功' : '登录失败',
        ]);
    }
}