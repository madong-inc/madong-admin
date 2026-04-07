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

namespace app\adminapi\event;

use app\enum\review\ReviewStatus;
use app\model\review\Review;
use Webman\Event\Event;

/**
 * 审核通过事件
 */
class ReviewApprovedEvent
{
    /**
     * 审核记录
     */
    public Review $review;
    
    /**
     * 额外数据
     */
    public array $data;
    
    /**
     * 构造函数
     */
    public function __construct(Review $review, array $data = [])
    {
        $this->review = $review;
        $this->data = $data;
    }
    
    /**
     * 触发事件
     */
    public function dispatch(): void
    {
        Event::emit('adminapi.review.approved', $this);
    }
}