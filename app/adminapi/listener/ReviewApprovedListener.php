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

use app\adminapi\event\ReviewApprovedEvent;
use app\enum\review\ReviewStatus;
use app\model\review\Review;
use support\Log;

/**
 * 审核通过事件监听器
 */
class ReviewApprovedListener
{
    /**
     * 处理审核通过事件
     *
     * @param ReviewApprovedEvent $event
     *
     * @return void
     */
    public function handle(ReviewApprovedEvent $event): void
    {
        try {
            $review = $event->review;
            
            // 记录日志
            Log::channel('default')->info('审核通过', [
                'review_id'        => $review->id,
                'reviewable_type'  => $review->reviewable_type,
                'reviewable_id'    => $review->reviewable_id,
                'reviewer_id'      => $review->reviewer_id,
            ]);

            // 根据不同的审核类型处理业务逻辑
            $this->handleReviewable($review, ReviewStatus::APPROVED->value);

        } catch (\Throwable $e) {
            Log::channel('default')->error('审核通过事件处理失败', [
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 更新审核记录状态
     *
     * @param Review $review 审核记录
     * @param int $status 审核状态
     */
    protected function handleReviewable(Review $review, int $status): void
    {
        $review->status = $status;
        $review->reviewed_at = time();
        $review->save();
    }
}