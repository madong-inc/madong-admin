<?php
declare(strict_types=1);

namespace app\service\admin\member;

use app\dao\member\MemberDao;
use app\dao\member\MemberPointsDao;
use app\enum\member\PointType;
use app\enum\member\PointSource;
use app\adminapi\event\PointsChangedEvent;
use core\base\BaseService;
use core\exception\handler\AdminException;
use support\Container;

/**
 * 会员积分服务类
 */
class MemberPointsService extends BaseService
{

    /**
     * 构造方法
     */
    public function __construct(MemberPointsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 积分操作
     *
     * @throws \core\exception\handler\AdminException
     * @throws \Throwable
     */
    public function operate(array $data): void
    {
        try {
            $this->transaction(function () use ($data) {
                $memberDao = Container::make(MemberDao::class);
                $member    = $memberDao->get($data['member_id']);
                $oldPoints = $member->points;
                if (!$member) {
                    throw new AdminException('会员不存在');
                }
                $points = (int)$data['points'];
                $type   = (int)$data['type'];
                $remark = $data['remark'] ?? '';

                // 更新会员积分
                if ($type == PointType::INCREASE->value) {
                    $member->points += $points;
                }
                if ($type == PointType::DECREASE->value) {
                    if ($member->points < $points) {
                        throw new AdminException('积分不足');
                    }
                    $member->points -= $points;
                }
                if($type == PointType::ADJUST->value){
                     $member->points = $points;
                }
                $member->save();
                // 记录积分变动
                $pointsData   = [
                    'member_id'   => $member->id,
                    'points'      => $points,
                    'type'        => $type,
                    'balance'     => $member->points,
                    'remark'      => $remark,
                ];
                $this->dao->save($pointsData);
                
                $event = new PointsChangedEvent(
                    $member->id,
                    $points,
                    PointSource::ADMIN,
                    PointType::INCREASE,
                    $oldPoints,
                    $member->points,
                    $remark
                );
                $event->dispatch();
            });

        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }
//
//    /**
//     * 批量积分操作
//     */
//    public function batchOperate(array $data): array
//    {
//        $memberIds = $data['member_ids'] ?? [];
//        if (empty($memberIds)) {
//            throw new AdminException('请选择会员');
//        }
//
//        $results = [];
//        foreach ($memberIds as $memberId) {
//            try {
//                $operateData = array_merge($data, ['member_id' => $memberId]);
//                $result = $this->operate($operateData);
//                $results[] = $result;
//            } catch (xception $e) {
//                $results[] = ['member_id' => $memberId, 'error' => $e->getMessage()];
//            }
//        }
//
//        return $results;
//    }
//
//    /**
//     * 获取积分统计
//     */
//    public function getStatistics(array $params): array
//    {
//        $startTime = $params['start_time'] ?? null;
//        $endTime = $params['end_time'] ?? null;
//
//        $where = [];
//        if ($startTime) {
//            $where[] = ['create_time', '>=', strtotime($startTime)];
//        }
//        if ($endTime) {
//            $where[] = ['create_time', '<=', strtotime($endTime) + 86399];
//        }
//
//        $totalIncome = $this->dao->query()->where($where)->where('type', 1)->sum('points') ?: 0;
//        $totalExpense = $this->dao->query()->where($where)->where('type', 2)->sum('points') ?: 0;
//        $totalRecords = $this->dao->query()->where($where)->count();
//
//        return [
//            'total_income' => $totalIncome,
//            'total_expense' => $totalExpense,
//            'net_income' => $totalIncome - $totalExpense,
//            'total_records' => $totalRecords,
//        ];
//    }
//
//    /**
//     * 设置积分规则
//     */
//    public function setRules(array $data): array
//    {
//        $rules = [
//            'sign_points' => $data['sign_points'] ?? 10,
//            'login_points' => $data['login_points'] ?? 5,
//            'max_sign_days' => $data['max_sign_days'] ?? 30,
//        ];
//
//        $config = $this->configDao->getByKey('member_points_rules');
//        if ($config) {
//            $config->value = json_encode($rules);
//            $config->save();
//        } else {
//            $this->configDao->save([
//                'key' => 'member_points_rules',
//                'value' => json_encode($rules),
//                'name' => '会员积分规则',
//                'group' => 'member',
//                'type' => 'json',
//            ]);
//        }
//
//        return $rules;
//    }
//
//    /**
//     * 获取积分规则
//     */
//    public function getRules(): array
//    {
//        $config = $this->configDao->getByKey('member_points_rules');
//        if ($config) {
//            return json_decode($config->value, true) ?: [];
//        }
//
//        // 默认规则
//        return [
//            'sign_points' => 10,
//            'login_points' => 5,
//            'max_sign_days' => 30,
//        ];
//    }
}
