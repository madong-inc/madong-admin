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

namespace app\admin\controller\system;

use app\admin\controller\Crud;
use app\admin\validate\system\SystemMessageValidate;
use app\common\enum\system\MessageStatus;
use app\common\services\system\SystemMessageService;
use madong\services\email\MessagePushService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemMessageController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->validate = Container::make(SystemMessageValidate::class);
        $this->service  = Container::make(SystemMessageService::class);
    }

    /**
     * 消息列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        $uid                 = getCurrentUser();
        $data                = $request->get();
        $data['receiver_id'] = $uid;//消息接收人
        $request->setGet($data);
        return parent::index($request);
    }

    /**
     * 消息状态标记
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function updateRead(Request $request): \support\Response
    {
        try {
            $data = $request->all();
            if ($this->validate) {
                $this->validate->scene('update-read')->check($data);
            }
            $actionMap = [
                MessageStatus::READ->value   => 'isRead',
                MessageStatus::UNREAD->value => 'isUnread',
            ];

            if (!isset($actionMap[$data['status']])) {
                throw new \InvalidArgumentException('Invalid message status');
            }

            $this->service->{$actionMap[$data['status']]}(
                $data['id'] ?? throw new \InvalidArgumentException('Missing message ID')
            );
            return Json::success('ok');
        } catch (\Throwable $e) {
            // 统一异常处理（生产环境建议记录日志）
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 推送广播&消息
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function notifyOnFirstLoginToAll(Request $request): \support\Response
    {
        try {
            $id = getCurrentUser();
            $this->service->notifyOnFirstLoginToAll($id);
            return Json::success('ok');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }
}
