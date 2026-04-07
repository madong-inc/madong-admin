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

use app\adminapi\event\PointsChangedEvent;
use app\adminapi\event\MemberLevelUpdatedEvent;
use app\dao\member\MemberDao;
use app\dao\member\MemberLevelDao;
use app\dao\member\MemberPointsDao;
use app\model\member\MemberLevel;
use app\enum\member\PointType;
use app\enum\common\EnabledStatus;
use support\Container;

/**
 * 积分变动监听器
 * 
 * 处理积分变动的事件：
 * 1. 更新会员积分
 * 2. 记录积分流水
 * 3. 触发会员等级更新事件
 */
class PointsChangedListener
{
    /**
     * 处理积分变动事件
     *
     * @throws \Exception
     */
    public function handle(PointsChangedEvent $event): void
    {
        // 更新会员积分
        $this->updateMemberPoints($event);
        
        // 记录积分流水
        $this->recordPointsLog($event);
        
        // 触发会员等级更新事件
        $this->updateMemberLevel($event);
    }
    
    /**
     * 更新会员积分
     */
    private function updateMemberPoints(PointsChangedEvent $event): void
    {
        /** @var MemberDao $dao */
        $dao = Container::make(MemberDao::class);
        $member = $dao->get($event->memberId);
        
        if (!$member) {
            return;
        }
        
        $member->points = $event->newPoints;
        $member->save();
    }

    /**
     * 记录积分流水
     *
     * @throws \Exception
     */
    private function recordPointsLog(PointsChangedEvent $event): void
    {
        /** @var MemberPointsDao $dao */
        $dao = Container::make(MemberPointsDao::class);
        
        $dao->save([
            'member_id' => $event->memberId,
            'type' => $event->type->value,
            'points' => $event->points,
            'balance' => $event->newPoints,
            'remark' => $event->remark,
            'related_id' => $event->relatedId
        ]);
    }

    /**
     * 更新会员等级
     *
     * @throws \Exception
     */
    private function updateMemberLevel(PointsChangedEvent $event): void
    {
        $memberId = $event->memberId;
        $newPoints = $event->newPoints;
        
        // 获取会员对象
        /** @var MemberDao $dao */
        $dao = Container::make(MemberDao::class);
        $member = $dao->get($memberId);
        
        if (!$member) {
            return;
        }
        
        $oldLevelId = $member->level_id;
        $oldLevelName = null;
        
        /** @var MemberLevelDao $memberLevelDao */
        $memberLevelDao = Container::make(MemberLevelDao::class);
        // 获取旧等级名称
        if ($oldLevelId) {
            $oldLevel = $memberLevelDao->get($oldLevelId);
            if ($oldLevel) {
                $oldLevelName = $oldLevel->name;
            }
        }
        
        // 根据新积分获取等级
        $newLevel = $memberLevelDao->query()
            ->where('enabled', EnabledStatus::ENABLED->value)
            ->where('min_points', '<=', $newPoints)
            ->where(function ($query) use ($newPoints) {
                $query->where('max_points', '>=', $newPoints)
                    ->orWhere('max_points', 0);
            })
            ->orderBy('level', 'desc')
            ->first();
        
        // 如果等级存在且与当前等级不同，更新等级
        if ($newLevel && $newLevel->id != $member->level_id) {
            $member->level_id = $newLevel->id;
            $member->save();
            
            $levelEvent = new MemberLevelUpdatedEvent(
                $memberId,
                $oldLevelId,
                $newLevel->id,
                $oldLevelName,
                $newLevel->name,
                $event->type === PointType::INCREASE ? 'points_increase' : 'points_deducted',
                ''
            );
            $levelEvent->dispatch();
        }
    }
}