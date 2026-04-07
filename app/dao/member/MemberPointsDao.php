<?php
declare(strict_types=1);

namespace app\dao\member;

use app\model\member\MemberPoints;
use core\base\BaseDao;

/**
 * 会员积分DAO类
 */
class MemberPointsDao extends BaseDao
{

    /**
     * 设置模型
     */
    public function setModel(): string
    {
        return MemberPoints::class;
    }

    /**
     * 获取会员积分记录
     */
    public function getMemberPoints(int|string $memberId, int $limit = 20): array
    {
        return $this->selectList(
            [['member_id', '=', $memberId]],
            '*',
            0, $limit,
            'created_at desc'
        )->toArray();
    }

    /**
     * 获取会员积分余额
     */
    public function getMemberBalance(int|string $memberId): float
    {
        $lastRecord = $this->query()
            ->where('member_id', '=', $memberId)
            ->orderBy('created_at', 'desc')
            ->first();
        return $lastRecord ? $lastRecord->balance : 0;
    }
}