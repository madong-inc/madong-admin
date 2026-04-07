<?php
declare(strict_types=1);

namespace app\dao\review;

use app\enum\review\ReviewStatus;
use app\model\review\Review;
use core\base\BaseDao;

/**
 * 审核DAO
 */
class ReviewDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return Review::class;
    }

    /**
     * 获取待审核数量
     *
     * @param string|null $reviewableType 审核类型
     * @return int
     * @throws \Exception
     */
    public function getPendingCount(?string $reviewableType = null): int
    {
        $query = $this->getModel()->query()->where('status', ReviewStatus::PENDING->value);
        if ($reviewableType) {
            $query->where('reviewable_type', $reviewableType);
        }
        return $query->count();
    }

    /**
     * 根据关联对象获取审核记录
     *
     * @param string $reviewableType 模型类型
     * @param int|string $reviewableId 模型ID
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getByReviewable(string $reviewableType, int|string $reviewableId): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->getOne([
            'reviewable_type' => $reviewableType,
            'reviewable_id'   => $reviewableId,
        ]);
    }

    /**
     * 批量审核通过
     *
     * @param array $ids 审核ID数组
     * @param int $reviewerId 审核人ID
     * @return int
     * @throws \Exception
     */
    public function batchApprove(array $ids, int $reviewerId): int
    {
        return $this->getModel()->query()
            ->whereIn('id', $ids)
            ->where('status', ReviewStatus::PENDING->value)
            ->update([
                'status'      => ReviewStatus::APPROVED->value,
                'reviewer_id' => $reviewerId,
                'reviewed_at' => time(),
            ]);
    }

    /**
     * 批量审核拒绝
     *
     * @param array $ids 审核ID数组
     * @param int $reviewerId 审核人ID
     * @param string $reason 拒绝原因
     * @return int
     * @throws \Exception
     */
    public function batchReject(array $ids, int $reviewerId, string $reason = ''): int
    {
        $data = [
            'status'      => ReviewStatus::REJECTED->value,
            'reviewer_id' => $reviewerId,
            'reviewed_at' => time(),
        ];
        if (!empty($reason)) {
            $data['reason'] = $reason;
        }
        return $this->getModel()->query()
            ->whereIn('id', $ids)
            ->where('status', ReviewStatus::PENDING->value)
            ->update($data);
    }

    /**
     * 查询列表（支持 extra_data 字段搜索）
     *
     * @param array $where
     * @param string|array $field
     * @param int $page
     * @param int $limit
     * @param string $order
     * @param array $with
     * @param bool $search
     * @param array|null $withoutScopes
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function selectList(array $where, string|array $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        // 获取基础查询构建器
        $query = $this->getModel()->query();
        
        // 处理作用域
        if (!empty($withoutScopes)) {
            $this->applyScopeRemoval($query, $withoutScopes);
        }
        
        // 处理 extra_data 字段搜索条件
        $this->applyExtraDataSearch($query, $where);
        
        // 处理其他普通条件（排除已处理的LIKE_*条件）
        $normalWhere = $this->filterExtraDataConditions($where);
        if (!empty($normalWhere)) {
            $options = [
                'keyword_fields' => $this->getKeywordFields(),
            ];
            
            // $search 为 false 时,禁用搜索器
            if (!$search) {
                $options['scopes'] = [];
            }
            
            $query = $this->applyQueryParams($query, $normalWhere, $options);
        }
        
        // 应用字段选择
        $isWildcard = ($field === '*' || ($field === ['*']));
        if (!$isWildcard) {
            if (is_array($field)) {
                $field = implode(',', $field);
            }
            $query->selectRaw($field);
        }
        
        // 应用排序
        if ($order !== '') {
            $query->orderByRaw($order);
        }
        
        // 应用关联加载
        if (!empty($with)) {
            $query->with($with);
        }
        
        // 应用分页或返回所有数据
        if ($page > 0 && $limit > 0) {
            return $query->paginate($limit, ['*'], 'page', $page)->getCollection();
        }
        return $query->get();
    }

    /**
     * 获取数量（支持 extra_data 字段搜索）
     *
     * @param array $where
     * @param bool $search
     * @return int
     * @throws \Exception
     */
    public function count(array $where = [], bool $search = false): int
    {
        // 获取基础查询构建器
        $query = $this->getModel()->query();
        
        // 处理 extra_data 字段搜索条件
        $this->applyExtraDataSearch($query, $where);
        
        // 处理其他普通条件（排除已处理的LIKE_*条件）
        $normalWhere = $this->filterExtraDataConditions($where);
        if (!empty($normalWhere)) {
            $options = [
                'keyword_fields' => $this->getKeywordFields(),
            ];
            
            // $search 为 false 时,禁用搜索器
            if (!$search) {
                $options['scopes'] = [];
            }
            
            $query = $this->applyQueryParams($query, $normalWhere, $options);
        }
        
        return $query->count();
    }

    /**
     * 处理 extra_data 字段搜索条件
     * 支持搜索 extra_data JSON 字段中的标题、申请人、内容等字段
     *
     * @param array $where
     * @return array
     */
    protected function processExtraDataSearch(array $where): array
    {
        $processedWhere = [];
        
        foreach ($where as $key => $value) {
            if (is_string($key) && preg_match('/^LIKE_(title|applicant|content)$/i', $key, $matches)) {
                // 处理 LIKE_title, LIKE_applicant, LIKE_content 等搜索条件
                // 这些字段存储在 extra_data JSON 中
                $fieldName = strtolower($matches[1]); // title, applicant, content
                
                // 转换为 JSON 查询条件
                // JSON_EXTRACT(extra_data, '$.title') LIKE '%值%'
                $processedWhere[] = [
                    'RAW',
                    "JSON_EXTRACT(extra_data, ?) LIKE ?",
                    ["$.{$fieldName}", "%{$value}%"]
                ];
            } else {
                // 其他条件保持不变
                $processedWhere[$key] = $value;
            }
        }
        
        return $processedWhere;
    }
    
    /**
     * 直接应用 extra_data 字段搜索到查询构建器（使用闭包查询）
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $where
     * @return void
     */
    protected function applyExtraDataSearch(\Illuminate\Database\Eloquent\Builder $query, array $where): void
    {
        $extraDataConditions = [];
        
        // 收集所有extra_data搜索条件
        foreach ($where as $key => $value) {
            if (is_string($key) && preg_match('/^LIKE_(title|applicant|content)$/i', $key, $matches)) {
                $fieldName = strtolower($matches[1]); // title, applicant, content
                $extraDataConditions[] = [
                    'field' => $fieldName,
                    'value' => $value
                ];
            }
        }
        
        // 如果有extra_data搜索条件，使用闭包查询
        if (!empty($extraDataConditions)) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $subQuery) use ($extraDataConditions) {
                foreach ($extraDataConditions as $condition) {
                    $subQuery->orWhereRaw(
                        "JSON_EXTRACT(extra_data, ?) LIKE ?", 
                        ["$.{$condition['field']}", "%{$condition['value']}%"]
                    );
                }
            });
        }
    }
    
    /**
     * 过滤掉已处理的 extra_data 搜索条件
     *
     * @param array $where
     * @return array
     */
    protected function filterExtraDataConditions(array $where): array
    {
        $filteredWhere = [];
        
        foreach ($where as $key => $value) {
            if (!is_string($key) || !preg_match('/^LIKE_(title|applicant|content)$/i', $key)) {
                $filteredWhere[$key] = $value;
            }
        }
        
        return $filteredWhere;
    }
}
