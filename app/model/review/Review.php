<?php
declare(strict_types=1);

namespace app\model\review;

use app\enum\review\ReviewStatus;
use app\model\system\Admin;
use core\base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 通用审核模型
 * 
 * 支持两种模式：
 * 1. 简单审核模式（flow_instance_id 为空）：直接审核通过/拒绝
 * 2. 审批流模式（flow_instance_id 不为空）：关联第三方审批流实例，由审批流模块处理
 */
class Review extends BaseModel
{
    use SoftDeletes;

    /**
     * 重写 booted 方法，不添加数据权限全局作用域
     * Review 表不需要数据权限控制
     */
    protected static function booted(): void
    {
        // 不调用父类的 booted，避免添加 AccessPermissionScope
    }

    /**
     * 数据表名称
     *
     * 注意：表名不要包含前缀，前缀由数据库配置自动添加
     */
    protected $table = 'sys_review';

    /**
     * 数据表主键
     */
    protected $primaryKey = 'id';

    /**
     * 可批量赋值的字段
     */
    protected $fillable = [
        'id',
        'reviewable_type',      // 关联模型类型
        'reviewable_id',        // 关联模型ID
        'status',               // 审核状态
        'reason',               // 审核原因/备注
        'reviewer_id',          // 审核人ID（简单模式）
        'reviewed_at',          // 审核时间
        'flow_instance_id',     // 审批流实例ID（审批流模式，关联第三方审批流）
        'extra_data',           // 扩展数据（JSON格式）
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 字段类型转换
     */
    protected $casts = [
        'extra_data' => 'array',
    ];

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * 追加字段
     */
    protected $appends = [
        'status_text',
        'is_workflow_mode',
    ];

    /**
     * 获取审核状态文本
     */
    public function getStatusTextAttribute(): string
    {
        return ReviewStatus::getLabel($this->status);
    }

    /**
     * 是否为审批流模式
     */
    public function getIsWorkflowModeAttribute(): bool
    {
        return !empty($this->flow_instance_id);
    }

    // ==================== 关联关系 ====================

    /**
     * 多态关联 - 关联的审核对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reviewable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 关联审核人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewer_id', 'id');
    }

    /**
     * 关联审批流实例（第三方模块）
     *
     * 注意：此方法需要在第三方审批流模块中实现具体的关联逻辑
     * 这里只是一个预留接口，返回动态关联
     *
     * 使用示例：
     * $review->flowInstance; // 获取审批流实例信息
     */
    public function flowInstance(): mixed
    {
        // 预留接口，由第三方审批流模块实现
        // 可以通过服务容器动态获取审批流实例
        if (empty($this->flow_instance_id)) {
            return null;
        }

        // 示例：假设第三方审批流模块提供了一个 FlowInstance 模型
        // 使用时需要根据第三方模块的具体实现调整
        $flowModelClass = config('review.flow_model', null);
        if ($flowModelClass && class_exists($flowModelClass)) {
            return $this->belongsTo($flowModelClass, 'flow_instance_id', 'id');
        }

        return null;
    }


    /**
     * 搜索器 - 按状态查询
     */
    public function scopeStatus($query, $status)
    {
        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }
        return $query;
    }

    /**
     * 搜索器 - 按审核类型查询
     */
    public function scopeReviewableType($query, $type)
    {
        if (!empty($type)) {
            $query->where('reviewable_type', $type);
        }
        return $query;
    }

    /**
     * 搜索器 - 按审批流实例ID查询
     */
    public function scopeFlowInstanceId($query, $flowInstanceId)
    {
        if (!empty($flowInstanceId)) {
            $query->where('flow_instance_id', $flowInstanceId);
        }
        return $query;
    }

    /**
     * 搜索器 - 按创建时间范围查询
     */
    public function scopeCreateTimeRange($query, $range)
    {
        if (!empty($range) && is_array($range) && count($range) === 2) {
            $query->whereBetween('created_at', $range);
        }
        return $query;
    }


    /**
     * 是否为简单审核模式
     */
    public function isSimpleMode(): bool
    {
        return empty($this->flow_instance_id);
    }

    /**
     * 是否为审批流模式
     */
    public function isWorkflowMode(): bool
    {
        return !empty($this->flow_instance_id);
    }

    /**
     * 是否可以审核
     *
     * 简单模式：检查是否待审核状态
     * 审批流模式：由第三方审批流模块判断（需要调用方自行处理）
     */
    public function canReview(int $userId): bool
    {
        // 简单审核模式
        if ($this->isSimpleMode()) {
            return $this->status === ReviewStatus::PENDING->value;
        }

        // 审批流模式 - 由第三方审批流模块判断
        // 这里返回 false，具体逻辑由调用方通过第三方接口判断
        return false;
    }

    /**
     * 获取审批流实例信息（从第三方模块）
     *
     * @return array|null
     */
    public function getFlowInstanceInfo(): ?array
    {
        if (empty($this->flow_instance_id)) {
            return null;
        }

        // 预留接口，由第三方审批流模块实现
        // 示例：
        // $flowService = app(config('review.flow_service'));
        // return $flowService->getInstanceInfo($this->flow_instance_id);

        return null;
    }
}
