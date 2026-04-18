<?php
declare(strict_types=1);

namespace app\service\admin\review;

use app\adminapi\CurrentUser;
use app\dao\review\ReviewDao;
use app\enum\review\ReviewStatus;
use app\adminapi\event\ReviewApprovedEvent;
use app\adminapi\event\ReviewRejectedEvent;
use app\model\review\Review;
use core\base\BaseService;
use core\exception\handler\AdminException;
use Illuminate\Support\Collection;
use support\Container;

/**
 * 审核服务类
 * 支持两种审核模式：
 * 1. 简单审核模式：直接审核通过/拒绝
 * 2. 审批流模式：关联第三方审批流实例，由审批流模块处理
 */
class ReviewService extends BaseService
{
    /**
     * 构造方法
     */
    public function __construct(ReviewDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 创建审核记录
     *
     * @param string     $reviewableType 模型类型
     * @param int|string $reviewableId   模型ID
     * @param array      $options        额外参数
     *                                   - reason: string 审核原因
     *                                   - flow_instance_id: string|null 审批流实例ID（传入则使用审批流模式）
     *                                   - extra_data: array 扩展数据
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Throwable
     */
    public function createReview(string $reviewableType, int|string $reviewableId, array $options = []): ?\Illuminate\Database\Eloquent\Model
    {
        try {
            // 检查是否已存在审核记录（无论状态，避免重复创建）
            $exists = $this->dao->getByReviewable($reviewableType, $reviewableId);
            if ($exists) {
                throw new AdminException('该记录已有审核记录');
            }

            $data = [
                'reviewable_type' => $reviewableType,
                'reviewable_id'   => $reviewableId,
                'status'          => ReviewStatus::PENDING->value,
            ];

            // 审核原因
            if (!empty($options['reason'])) {
                $data['reason'] = $options['reason'];
            }

            // 审批流实例ID（审批流模式）
            if (!empty($options['flow_instance_id'])) {
                $data['flow_instance_id'] = $options['flow_instance_id'];
            }

            // 扩展数据
            if (!empty($options['extra_data'])) {
                $data['extra_data'] = $options['extra_data'];
            }

            return $this->dao->save($data);
        } catch (\Throwable $e) {
            throw new AdminException('创建审核记录失败: ' . $e->getMessage());
        }
    }

    /**
     * 审核通过
     *
     * @param int|string $id         审核记录ID
     * @param int|null   $reviewerId 审核人ID（默认当前用户）
     * @param array      $options    额外参数
     *                               - reason: string 审核原因/备注
     *                               - flow_callback: bool 是否为审批流回调（内部使用）
     *
     * @return bool
     * @throws \Throwable
     */
    public function approve(int|string $id, ?int $reviewerId = null, array $options = []): bool
    {
        return $this->transaction(function () use ($id, $reviewerId, $options) {
            return $this->approveWithoutTransaction($id, $reviewerId, $options);
        });
    }

    /**
     * 审核通过（不带事务）
     * 用于事件监听器等已经在事务中的场景
     *
     * @param int|string $id         审核记录ID
     * @param int|null   $reviewerId 审核人ID（默认当前用户）
     * @param array      $options    额外参数
     *                               - reason: string 审核原因/备注
     *                               - flow_callback: bool 是否为审批流回调（内部使用）
     *
     * @return bool
     * @throws \Throwable
     */
    public function approveWithoutTransaction(int|string $id, ?int $reviewerId = null, array $options = []): bool
    {
        $review = $this->dao->get($id);
        if (empty($review)) {
            throw new AdminException('审核记录不存在');
        }
        if ($review->status !== ReviewStatus::PENDING->value) {
            throw new AdminException('该记录已审核');
        }

        // 判断审核模式
        if ($review->isWorkflowMode() && empty($options['flow_callback'])) {
            // 审批流模式：调用第三方审批流模块
            return $this->handleWorkflowApprove($review, $reviewerId, $options);
        }

        // 简单审核模式：直接审核通过
        $reviewerId          = $reviewerId ?? config('review.auto_review.reviewer_id', 0);
        $review->status      = ReviewStatus::APPROVED->value;
        $review->reviewer_id = $reviewerId;
        $review->reviewed_at = time();

        if (!empty($options['reason'])) {
            $review->reason = $options['reason'];
        }

        $review->save();

        // 触发审核通过事件
        $event = new ReviewApprovedEvent($review);
        $event->dispatch();

        // 执行审核通过回调（配置中的回调）
        $this->executeApprovedCallbacks($review);

        return true;
    }

    /**
     * 执行审核通过回调
     *
     * @param \app\model\review\Review $review
     * @return void
     */
    protected function executeApprovedCallbacks($review): void
    {
        try {
            // 通过反射获取 ReviewFieldMapper 的 configCache（包含完整插件配置）
            $typeKey = $this->getReviewTypeKey($review);
            $typeConfig = $this->getReviewTypeConfigFromMapper($typeKey);

            if (empty($typeConfig['callbacks']['approved'])) {
                return;
            }

            $callbacks = $typeConfig['callbacks']['approved'];

            // 支持单个类名或类名数组
            if (is_string($callbacks)) {
                $callbacks = [$callbacks];
            }

            foreach ($callbacks as $class) {
                if (!class_exists($class)) {
                    continue;
                }

                $instance = new $class();

                if (method_exists($instance, 'handle')) {
                    $instance->handle($review);
                }
            }
        } catch (\Throwable $e) {
            // 回调执行失败不影响审核流程
        }
    }

    /**
     * 获取审核类型 key
     */
    protected function getReviewTypeKey($review): string
    {
        // 尝试通过 morph_map 查找别名
        $morphMap = config('morph_map.map', []);
        $alias = array_search($review->reviewable_type, $morphMap, true);
        
        return $alias !== false ? $alias : $review->reviewable_type;
    }

    /**
     * 从 ReviewFieldMapper 获取完整类型配置
     */
    protected function getReviewTypeConfigFromMapper(string $typeKey): ?array
    {
        try {
            // 先调用 getTypeConfig 确保 init() 被执行，configCache 被填充
            ReviewFieldMapper::getTypeConfig($typeKey);
            
            // 使用反射获取 configCache
            $reflection = new \ReflectionClass(ReviewFieldMapper::class);
            $property = $reflection->getProperty('configCache');
            $property->setAccessible(true);
            $configCache = $property->getValue();
            
            // configCache 结构: ['types' => [...], 'field_mappings' => [...]]
            return $configCache['types'][$typeKey] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * 审核拒绝
     *
     * @param int|string $id         审核记录ID
     * @param string     $reason     拒绝原因
     * @param int|null   $reviewerId 审核人ID（默认当前用户）
     * @param array      $options    额外参数
     *                               - flow_callback: bool 是否为审批流回调（内部使用）
     *
     * @return bool
     * @throws \Throwable
     */
    public function reject(int|string $id, string $reason = '', ?int $reviewerId = null, array $options = []): bool
    {
        return $this->transaction(function () use ($id, $reason, $reviewerId, $options) {
            return $this->rejectWithoutTransaction($id, $reason, $reviewerId, $options);
        });
    }

    /**
     * 审核拒绝（不带事务）
     * 用于事件监听器等已经在事务中的场景
     *
     * @param int|string $id         审核记录ID
     * @param string     $reason     拒绝原因
     * @param int|null   $reviewerId 审核人ID（默认当前用户）
     * @param array      $options    额外参数
     *                               - flow_callback: bool 是否为审批流回调（内部使用）
     *
     * @return bool
     * @throws \Throwable
     */
    public function rejectWithoutTransaction(int|string $id, string $reason = '', ?int $reviewerId = null, array $options = []): bool
    {
        $review = $this->dao->get($id);
        if (empty($review)) {
            throw new AdminException('审核记录不存在');
        }
        if ($review->status !== ReviewStatus::PENDING->value) {
            throw new AdminException('该记录已审核');
        }

        // 判断审核模式
        if ($review->isWorkflowMode() && empty($options['flow_callback'])) {
            // 审批流模式：调用第三方审批流模块
            return $this->handleWorkflowReject($review, $reason, $reviewerId, $options);
        }

        // 简单审核模式：直接审核拒绝
        $reviewerId          = $reviewerId ?? config('review.auto_review.reviewer_id', 0);
        $review->status      = ReviewStatus::REJECTED->value;
        $review->reviewer_id = $reviewerId;
        $review->reviewed_at = time();
        if (!empty($reason)) {
            $review->reason = $reason;
        }
        $review->save();

        return true;
    }

    /**
     * 批量审核通过
     *
     * @param array $ids     审核记录ID数组
     * @param array $options 额外参数
     *
     * @return int 审核通过的数量
     * @throws \Throwable
     */
    public function batchApprove(array $ids, array $options = []): int
    {
        return $this->transaction(function () use ($ids, $options) {
            // 直接使用 whereIn 查询待审核的记录，避免 selectList 的条件处理问题
            $pendingReviews = $this->dao->getModel()
                ->whereIn('id', $ids)
                ->where('status', ReviewStatus::PENDING->value)
                ->get(['id']);

            $pendingIds = $pendingReviews->pluck('id')->toArray();

            // 调试日志
            \support\Log::info('batchApprove', [
                'input_ids' => $ids,
                'pending_ids' => $pendingIds,
                'options' => $options
            ]);

            if (empty($pendingIds)) {
                return 0;
            }

            $reviewerId = Container::make(CurrentUser::class)->id();
            $reviewerId = $reviewerId ? (int)$reviewerId : null;
            $count      = 0;

            // 批量更新待审核的记录
            foreach ($pendingIds as $id) {
                try {
                    $review = $this->dao->get($id);
                    if (empty($review)) {
                        \support\Log::warning("Review not found: {$id}");
                        continue;
                    }
                    if ($review->status !== ReviewStatus::PENDING->value) {
                        \support\Log::warning("Review not pending: {$id}, status: {$review->status}");
                        continue;
                    }

                    // 判断审核模式
                    if ($review->isWorkflowMode() && empty($options['flow_callback'])) {
                        // 审批流模式：调用第三方审批流模块
                        $this->handleWorkflowApprove($review, $reviewerId, $options);
                    } else {
                        // 简单审核模式：直接审核通过
                        $review->status      = ReviewStatus::APPROVED->value;
                        $review->reviewer_id = $reviewerId;
                        $review->reviewed_at = time();

                        if (!empty($options['reason'])) {
                            $review->reason = $options['reason'];
                        }

                        $review->save();

                        $event = new ReviewApprovedEvent($review);
                        $event->dispatch();
                    }

                    $count++;
                    \support\Log::info("Approved review: {$id}, count: {$count}");
                } catch (\Exception $e) {
                    // 记录错误但继续处理其他记录
                    \support\Log::error("Failed to approve review: {$id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            \support\Log::info('batchApprove completed', ['total_count' => $count]);
            return $count;
        });
    }

    /**
     * 批量审核拒绝
     *
     * @param array  $ids     审核记录ID数组
     * @param string $reason  拒绝原因
     * @param array  $options 额外参数
     *
     * @return int 审核拒绝的数量
     * @throws \Throwable
     */
    public function batchReject(array $ids, string $reason = '', array $options = []): int
    {
        return $this->transaction(function () use ($ids, $reason, $options) {
            // 直接使用 whereIn 查询待审核的记录，避免 selectList 的条件处理问题
            $pendingReviews = $this->dao->getModel()
                ->whereIn('id', $ids)
                ->where('status', ReviewStatus::PENDING->value)
                ->get(['id']);

            $pendingIds = $pendingReviews->pluck('id')->toArray();

            // 调试日志
            \support\Log::info('batchReject', [
                'input_ids' => $ids,
                'pending_ids' => $pendingIds,
                'reason' => $reason,
                'options' => $options
            ]);

            if (empty($pendingIds)) {
                return 0;
            }

            $reviewerId = Container::make(CurrentUser::class)->id();
            $reviewerId = $reviewerId ? (int)$reviewerId : null;
            $count      = 0;

            // 批量更新待审核的记录
            foreach ($pendingIds as $id) {
                try {
                    $review = $this->dao->get($id);
                    if (empty($review)) {
                        \support\Log::warning("Review not found: {$id}");
                        continue;
                    }
                    if ($review->status !== ReviewStatus::PENDING->value) {
                        \support\Log::warning("Review not pending: {$id}, status: {$review->status}");
                        continue;
                    }

                    // 判断审核模式
                    if ($review->isWorkflowMode() && empty($options['flow_callback'])) {
                        // 审批流模式：调用第三方审批流模块
                        $this->handleWorkflowReject($review, $reason, $reviewerId, $options);
                    } else {
                        // 简单审核模式：直接审核拒绝
                        $review->status      = ReviewStatus::REJECTED->value;
                        $review->reviewer_id = $reviewerId;
                        $review->reviewed_at = time();
                        if (!empty($reason)) {
                            $review->reason = $reason;
                        }
                        $review->save();

                        $event = new ReviewRejectedEvent($review);
                        $event->dispatch();
                    }

                    $count++;
                    \support\Log::info("Rejected review: {$id}, count: {$count}");
                } catch (\Exception $e) {
                    // 记录错误但继续处理其他记录
                    \support\Log::error("Failed to reject review: {$id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            \support\Log::info('batchReject completed', ['total_count' => $count]);
            return $count;
        });
    }

    /**
     * 审批流回调 - 审核通过
     * 由第三方审批流模块调用，更新审核状态
     *
     * @param string   $flowInstanceId 审批流实例ID
     * @param int|null $reviewerId     审核人ID
     * @param string   $reason         审核原因
     *
     * @return bool
     * @throws \Throwable
     */
    public function flowApproveCallback(string $flowInstanceId, ?int $reviewerId = null, string $reason = ''): bool
    {
        $review = $this->dao->getOne(['flow_instance_id' => $flowInstanceId]);
        if (empty($review)) {
            throw new AdminException('审核记录不存在');
        }

        return $this->approve($review->id, $reviewerId, [
            'reason'        => $reason,
            'flow_callback' => true, // 标记为审批流回调
        ]);
    }

    /**
     * 审批流回调 - 审核拒绝
     * 由第三方审批流模块调用，更新审核状态
     *
     * @param string   $flowInstanceId 审批流实例ID
     * @param string   $reason         拒绝原因
     * @param int|null $reviewerId     审核人ID
     *
     * @return bool
     * @throws \Throwable
     */
    public function flowRejectCallback(string $flowInstanceId, string $reason = '', ?int $reviewerId = null): bool
    {
        $review = $this->dao->getOne(['flow_instance_id' => $flowInstanceId]);
        if (empty($review)) {
            throw new AdminException('审核记录不存在');
        }

        return $this->reject($review->id, $reason, $reviewerId, [
            'flow_callback' => true, // 标记为审批流回调
        ]);
    }

    /**
     * 获取审核统计
     *
     * @return array
     * @throws \Exception
     */
    public function getStatistics(): array
    {
        $total    = $this->dao->count();
        $pending  = $this->dao->getPendingCount();
        $approved = $this->dao->count(['status' => ReviewStatus::APPROVED->value]);
        $rejected = $this->dao->count(['status' => ReviewStatus::REJECTED->value]);

        return [
            'total'    => $total,
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }

    /**
     * 根据关联对象获取审核记录
     *
     * @param string     $reviewableType 模型类型
     * @param int|string $reviewableId   模型ID
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getByReviewable(string $reviewableType, int|string $reviewableId): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->dao->getByReviewable($reviewableType, $reviewableId);
    }

    /**
     * 根据审批流实例ID获取审核记录
     *
     * @param string $flowInstanceId 审批流实例ID
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getByFlowInstanceId(string $flowInstanceId): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->dao->getOne(['flow_instance_id' => $flowInstanceId]);
    }

    // ==================== 审批流模式处理 ====================

    /**
     * 处理审批流模式 - 审核通过
     * 调用第三方审批流模块的审核通过接口
     *
     * @param Review   $review     审核记录
     * @param int|null $reviewerId 审核人ID
     * @param array    $options    额外参数
     *
     * @return bool
     * @throws AdminException
     */
    protected function handleWorkflowApprove(Review $review, ?int $reviewerId, array $options): bool
    {
        // 调用第三方审批流模块的审核通过接口
        // 示例：
        // $flowService = app(config('review.flow_service'));
        // return $flowService->approve($review->flow_instance_id, $reviewerId, $options);

        // 未配置第三方审批流模块时，抛出异常
        throw new AdminException('审批流模块未配置，请联系管理员');
    }

    /**
     * 处理审批流模式 - 审核拒绝
     * 调用第三方审批流模块的审核拒绝接口
     *
     * @param Review   $review     审核记录
     * @param string   $reason     拒绝原因
     * @param int|null $reviewerId 审核人ID
     * @param array    $options    额外参数
     *
     * @return bool
     * @throws AdminException
     */
    protected function handleWorkflowReject(Review $review, string $reason, ?int $reviewerId, array $options): bool
    {
        // 调用第三方审批流模块的审核拒绝接口
        // 示例：
        // $flowService = app(config('review.flow_service'));
        // return $flowService->reject($review->flow_instance_id, $reason, $reviewerId, $options);

        // 未配置第三方审批流模块时，抛出异常
        throw new AdminException('审批流模块未配置，请联系管理员');
    }


    /**
     * 获取映射后的审核列表（支持插件配置）
     *
     * @param array  $where  查询条件
     * @param string $field  查询字段
     * @param int    $page   页码
     * @param int    $limit  每页数量
     * @param string $order  排序
     * @param array  $with   关联预加载
     * @param bool   $search 是否搜索模式
     *
     * @return Collection
     * @throws \Exception
     */
    public function getMappedList(array $where = [], string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false): Collection
    {
        $reviews       = $this->dao->selectList($where, $field, $page, $limit, $order, $with, $search);
        return ReviewFieldMapper::mapReviews($reviews);
    }

    /**
     * 获取单条映射后的审核记录
     *
     * @param int|string $id          审核记录ID
     * @param array      $extraFields 额外需要映射的字段
     *
     * @return array|null
     * @throws \Exception
     */
    public function getMappedReview(int|string $id, array $extraFields = []): ?array
    {
        $review = $this->dao->get($id,['*'], ['reviewer', 'reviewable']);
        if (!$review) {
            return null;
        }

        return ReviewFieldMapper::mapReview($review, $extraFields);
    }

    /**
     * 获取所有已配置的审核类型（用于前端下拉选择）
     *
     * @return array
     */
    public function getReviewTypes(): array
    {
        return ReviewFieldMapper::getConfiguredTypes();
    }

    /**
     * 获取指定审核类型的配置信息
     *
     * @param string $typeKey 类型键（morph_map别名）
     *
     * @return array|null
     */
    public function getReviewTypeConfig(string $typeKey): ?array
    {
        return ReviewFieldMapper::getTypeConfig($typeKey);
    }

    /**
     * 创建审核记录（增强版，自动填充extra_data）
     * 简化版：直接调用基础方法，extra_data由调用方提供
     *
     * @param string     $reviewableType 模型类型（可以是类名或morph_map别名）
     * @param int|string $reviewableId   模型ID
     * @param array      $options        额外参数
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Throwable
     */
    public function createReviewWithData(string $reviewableType, int|string $reviewableId, array $options = []): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->createReview($reviewableType, $reviewableId, $options);
    }

    /**
     * 根据morph_map别名获取模型类名
     */
    protected function getModelClassFromMorphAlias(string $alias): ?string
    {
        $morphMap = config('morph_map.map', []);
        return $morphMap[$alias] ?? null;
    }

    /**
     * 根据配置构建extra_data
     */
    protected function buildExtraDataFromConfig($reviewable, array $fieldConfigs): array
    {
        $extraData = [];

        foreach ($fieldConfigs as $fieldName => $config) {
            if (in_array($fieldName, ['title', 'content', 'applicant'])) {
                // 获取字段值
                $value                 = $this->getFieldValueFromConfig($reviewable, $fieldName, $config);
                $extraData[$fieldName] = $value;
            }
        }

        return $extraData;
    }

    // ==================== 搜索处理相关方法 ====================

    /**
     * 获取数量（支持 extra_data 字段搜索）
     *
     * @param array $where
     * @param bool  $search
     *
     * @return int
     * @throws \Exception
     */
    public function getCountWithSearch(array $where = [], bool $search = false): int
    {
        return $this->dao->count($where, $search);
    }

    /**
     * 根据配置获取字段值
     *
     * @param mixed  $model       模型对象
     * @param string $fieldName   字段名
     * @param array  $fieldConfig 字段配置
     *
     * @return mixed
     */
    protected function getFieldValueFromConfig($model, string $fieldName, array $fieldConfig): mixed
    {
        if (!$model || empty($fieldConfig)) {
            return $fieldConfig['fallback'] ?? null;
        }

        $type   = $fieldConfig['type'] ?? 'attribute';
        $source = $fieldConfig['source'] ?? $fieldName;

        switch ($type) {
            case 'attribute':
                // 直接获取属性
                return $model->{$source} ?? ($fieldConfig['fallback'] ?? null);

            case 'relation':
                // 通过关联获取
                if ($model->{$source} && isset($fieldConfig['attribute'])) {
                    return $model->{$source}->{$fieldConfig['attribute']} ?? ($fieldConfig['fallback'] ?? null);
                }
                return $fieldConfig['fallback'] ?? null;

            case 'callback':
                // 回调函数
                if (isset($fieldConfig['callback']) && is_callable($fieldConfig['callback'])) {
                    return call_user_func($fieldConfig['callback'], $model);
                }
                return $fieldConfig['fallback'] ?? null;

            case 'fixed':
                // 固定值
                return $fieldConfig['value'] ?? ($fieldConfig['fallback'] ?? null);

            default:
                return $fieldConfig['fallback'] ?? null;
        }
    }
}


