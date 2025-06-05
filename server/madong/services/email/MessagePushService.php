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

namespace madong\services\email;

use app\common\dao\system\SystemMessageDao;
use app\common\enum\system\MessagePriority;
use app\common\enum\system\MessageStatus;
use app\common\model\system\SystemMessage;
use app\common\model\system\SystemNotice;
use app\common\services\system\SystemNoticeService;
use madong\helper\Arr;
use madong\interface\IDict;
use Webman\Push\Api;

class MessagePushService
{
    protected static ?Api $api = null;

    /**
     * 初始化 API 实例
     */
    protected static function initApi(): void
    {
        if (self::$api === null) {
            self::$api = new Api(
                config('plugin.webman.push.app.api'), // 推送 API 地址
                config('plugin.webman.push.app.app_key'), // app_key
                config('plugin.webman.push.app.app_secret') // app_secret
            );
        }
    }

    /**
     * 广播公告
     *
     * @return void
     */
    public static function notificationBroadcast(): void
    {
        self::initApi();
        $noticeServer = new SystemNoticeService();
        $noticeModel  = $noticeServer->selectList(['enabled' => 1], '*', 0, 0, 'sort desc', []);
        foreach ($noticeModel as $model) {
            self::modelNotice($model);
        }
    }

    /**
     * 消息推送
     *
     * @param string|array                                $uid
     * @param \app\common\model\system\SystemMessage|null $model
     */
    public static function pushNotificationToUser(string|array $uid, ?SystemMessage $model = null): void
    {
        $data = Arr::normalize($uid);
        foreach ($data as $id) {
            if (!empty($model)) {
                self::modelMessage($model, $id);
            }
        }
    }

    /**
     * 广播消息到所有用户
     *
     * @param \app\common\model\system\SystemNotice $model
     */
    public static function modelNotice(SystemNotice $model): void
    {
        self::initApi();
        $data = [
            'id'           => (string)$model->getAttribute('id'),
            'title'        => $model->getAttribute('title'),//标题
            'content'      => $model->getAttribute('content'),//消息内容
            'receiver_id'  => null,//接受者id
            'status'       => MessageStatus::UNREAD->value,//消息状态
            'priority'     => MessagePriority::EMERGENCY,//优先级
            'channel'      => 'notice',//发送渠道
            'related_id'   => '0',//关联业务id
            'type'         => $model->getAttribute('type'),//类型
            'created_date' => $model->getAttribute('created_date') ?? '',
            'sender'       => [
                'id'        => 0,
                'real_name' => '系统管理员',
                'user_name' => 'administrator',
                'avatar'    => getAvatarUrl(null),
            ],
        ];
        if (!empty($data)) {
            self::$api->trigger("admin", 'notice', $data);
        }
    }

    /**
     * 消息推送
     *
     * @param \app\common\model\system\SystemMessage $model
     * @param string                                 $id
     */
    public static function modelMessage(SystemMessage $model, string $id): void
    {
        self::initApi();
        $data = [
            'id'           => (string)$model->getAttribute('id'),
            'title'        => $model->getAttribute('title'),//标题
            'content'      => $model->getAttribute('content'),//消息内容
            'receiver_id'  => (string)$id,//接受者id
            'status'       => $model->getAttribute('status'),//消息状态
            'priority'     => $model->getAttribute('priority'),//优先级
            'channel'      => $model->getAttribute('channel'),//发送渠道
            'related_id'   => (string)$model->getAttribute('related_id'),//关联业务id
            'type'         => $model->getAttribute('type'),//消息类型
            'created_date' => $model->getAttribute('created_date') ?? '',
            'sender'       => [
                'id'        => $model->sender->id ?? 0,
                'real_name' => $model->sender->real_name ?? '系统管理员',
                'user_name' => $model->sender->user_name ?? 'administrator',
                'avatar'    => getAvatarUrl($model->sender ?? null),
            ],
        ];
        if (!empty($data)) {
            self::$api->trigger("admin-$id", 'message', $data);
        }
    }

    /**
     * 消息推送-用户
     *
     * @param \madong\helper\Dict $dict
     * @param string|array        $id
     *
     * @throws \Exception
     */
    public static function informationToUser(IDict $dict, string|array $id): void
    {
        self::initApi();
        $data = Arr::normalize($id);
        foreach ($data as $i) {
            $row   = [
                'title'         => $dict->get('title'),//标题
                'content'       => $dict->get('content'),//消息内容
                'receiver_id'   => (string)$i,//接受者id
                'status'        => $dict->get('status', MessageStatus::UNREAD->value),//消息状态
                'priority'      => $dict->get('priority', MessagePriority::NORMAL->value),//优先级
                'channel'       => $dict->get('channel'),//发送渠道
                'related_id'    => (string)$dict->get('related_id'),//关联业务id
                'related_type'  => $dict->get('related_type'),//关联业务id
                'action_url'    => $dict->get('action_url'),//关联业务id
                'action_params' => $dict->get('action_params'),//关联业务id
                'type'          => $dict->get('type'),//消息类型
                'sender_id'     => $dict->get('sender_id'),
            ];
            $dao   = new SystemMessageDao();
            $model = $dao->save($row);
            if (!empty($model)) {
                //保存后的模型没有关联用户表重新加载单个模型载入数据
                $model->load('sender');
                self::modelMessage($model, $i);
            }
        }
    }
}
