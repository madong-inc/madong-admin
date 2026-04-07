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

use app\adminapi\event\ReviewCreatedEvent;
use support\Log;

/**
 * 审核创建事件监听器
 */
class ReviewCreatedListener
{
    /**
     * 处理审核创建事件
     *
     * @param ReviewCreatedEvent $event
     *
     * @return void
     */
    public function handle(ReviewCreatedEvent $event): void
    {
        try {
            $review = $event->review;

            // 记录日志
            Log::channel('default')->info('创建审核记录', [
                'review_id'        => $review->id,
                'reviewable_type'  => $review->reviewable_type,
                'reviewable_id'    => $review->reviewable_id,
            ]);

        } catch (\Throwable $e) {
            Log::channel('default')->error('审核创建事件处理失败', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}