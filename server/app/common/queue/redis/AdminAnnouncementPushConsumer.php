<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */

namespace app\common\queue\redis;

use app\common\services\system\SysAdminService;
use core\logger\Logger;
use core\notify\enum\PushClientType;
use core\notify\Notification;
use Webman\RedisQueue\Consumer;

/**
 * 推送公告-后台
 *
 * @author Mr.April
 * @since  1.0
 */
class AdminAnnouncementPushConsumer implements Consumer
{
    public string $queue = 'admin-announcement-push';
    public string $connection = 'default';

    /**
     * 消费公告推送消息
     *
     * @param array $data 消息数据
     *
     * @return bool
     * @throws \Throwable
     */
    public function consume($data): bool
    {
        try {
            Logger::debug('公告推送开始', $data);

            // 1. 验证必要参数
            $this->validateData($data);

            // 2. 获取目标用户ID列表
            $adminIds = $this->getTargetAdminIds($data['uuid'] ?? null);

            // 3. 构建并发送通知
            $this->sendNotifications($adminIds, $data, '*');

            Logger::debug("公告推送完成: {$data['title']}");
            return true;
        } catch (\Throwable $e) {
            Logger::error("公告推送失败: " . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
                'data'  => $data,
            ]);
            throw $e;
        }
    }

    /**
     * 验证消息数据
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     */
    private function validateData(array $data): void
    {
        $requiredFields = ['id', 'title', 'content'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("缺少必要字段: {$field}");
            }
        }
    }

    /**
     * 获取目标管理员ID列表
     *
     * @param string|null $uuid
     *
     * @return array
     */
    private function getTargetAdminIds(?string $uuid): array
    {
        $userService = new SysAdminService();
        $query       = $userService->getModel()
            ->where('enabled', 1);

        // 只有当uuid不为空时才检查消息
        if (!empty($uuid)) {
            $query->where(function ($q) use ($uuid) {
                $q->whereDoesntHave('message', function ($subQuery) use ($uuid) {
                    $subQuery->where('message_uuid', $uuid);
                })
                    ->orWhereHas('message', function ($subQuery) {
                        $subQuery->whereNull('message_uuid');
                    });
            });
        }

        return $query->pluck('id')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * 发送通知
     *
     * @param array      $adminIds
     * @param array      $data
     * @param int|string $tenantId
     */
    private function sendNotifications(array $adminIds, array $data, int|string $tenantId = '*'): void
    {
        if (empty($adminIds)) {
            Logger::warning('没有符合条件的目标用户', ['tenant_id' => $tenantId]);
            return;
        }

        $sendData = [];
        foreach ($adminIds as $id) {
            $sendData[] = [
                'module'       => 'admin',
                'receiver_id'  => $id,
                'event'        => 'message',
                'data'         => [],
                'title'        => $data['title'],
                'content'      => $data['content'],
                'message_type' => 'message',
                'priority'     => 1,
                'related_id'   => $data['id'] ?? '',
                'expired_at'   => time() + 86400 * 7,
                'message_uuid' => $data['uuid'] ?? null,
            ];
        }

        Notification::batchSend(PushClientType::BACKEND, $tenantId ?? '*', $sendData);
    }
}