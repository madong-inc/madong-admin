<?php
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

namespace core\abstract;

use madong\helper\Arr;
use support\Db;

/**
 * @method count(array $where = [], bool $search = true)
 * @method selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false)
 * @method selectModel(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false)
 * @method getCount(array $where)
 * @method getDistinctCount(array $where, $field, bool $search = true)
 * @method getPk()
 * @method getTableName()
 * @method get($id, ?array $field = [], ?array $with = [], string $order = '')
 * @method be($map, string $field = '')
 * @method getOne(array $where, ?string $field = '*', array $with = [])
 * @method value($where, ?string $field = '')
 * @method getColumn(array $where, string $field, string $key = '')
 * @method delete(array|int|string $id, ?string $key = null)
 * @method destroy(mixed $id, bool $force = false)
 * @method update(string|int|array $id, array $data, ?string $key = null)
 * @method setWhere($where, ?string $key = null)
 * @method batchUpdate(array $ids, array $data, ?string $key = null)
 * @method save(array $data)
 * @method saveAll(array $data)
 * @method getFieldValue($value, string $filed, ?string $valueKey = '', ?array $where = [])
 * @method search(array $where = [], bool $search = true)
 * @method sum(array $where, string $field, bool $search = false)
 * @method bcInc($key, string $incField, string $inc, string $keyField = null, int $acc = 2)
 * @method bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2)
 * @method getMax(array $where = [], string $field = '')
 * @method getMin(array $where = [], string $field = '')
 * @method decStockIncSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales')
 * @method incStockDecSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales')
 */

/**
 * 基础Dao
 *
 * @author Mr.April
 * @since  1.0
 */
abstract class BaseDao
{

    /**
     * 获取当前模型
     *
     * @return string
     */
    abstract protected function setModel(): string;

    /**
     * 获取条数
     *
     * @param array $where
     * @param bool  $search
     *
     * @return int
     * @throws \Exception
     */
    public function count(array $where = [], bool $search = false): int
    {
        // 获取查询构建器实例
        $query = $this->getModel()->query();
        if ($search) {
            $query = $this->search($where); // search 返回的是一个查询构建器
        } else {
            $query->where($where); // 应用 where 条件
        }

        // 返回满足条件的记录数量
        return $query->count();
    }

    /**
     * 查询列表
     *
     * @param array      $where
     * @param string     $field
     * @param int        $page
     * @param int        $limit
     * @param string     $order
     * @param array      $with
     * @param bool       $search
     * @param array|null $withoutScopes
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     * @throws \Exception
     */
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Collection
    {
        // 使用 selectModel 方法获取查询构建器
        $query = $this->selectModel($where, $field, $page, $limit, $order, $with, $search, $withoutScopes);

        // 如果字段不是 '*'，则应用 selectRaw()
        if ($field !== '*') {
            $query->selectRaw($field); // 确保在查询构建器上调用
        }
        // 应用分页
        if ($page > 0 && $limit > 0) {
            // 只返回数据部分
            return $query->paginate($limit, ['*'], 'page', $page)->getCollection();
        }
        return $query->get(); // 返回所有数据
    }

    /**
     * 获取某些条件数据
     *
     * @param array      $where
     * @param string     $field
     * @param int        $page
     * @param int        $limit
     * @param string     $order
     * @param array      $with
     * @param bool       $search
     * @param array|null $withoutScopes
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Pagination\LengthAwarePaginator|null
     * @throws \Exception
     */
    public function selectModel(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false, ?array $withoutScopes = null): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Pagination\LengthAwarePaginator|null
    {
        // 获取模型的查询构建器
        $query = $this->getModel()->query();

        // 作用域处理
        if (!empty($withoutScopes)) {
            $this->applyScopeRemoval($query, $withoutScopes);
        }

        // 根据是否需要搜索来决定查询条件
        if ($search) {
            $query = $this->search($where); // search 返回的是一个查询构建器
        } else {
//            $query->where($where); // 应用 where 条件
            $whereInConditions = [];
            foreach ($where as $key => $condition) {
                if (is_array($condition) && count($condition) === 3 && ($condition[1] === 'in' || $condition[1] === 'IN')) {
                    $whereInConditions[] = $condition;
                    unset($where[$key]);//移除分离后的条件
                }
            }
            //普通条件格式直接传入构建
            if (!empty($where)) {
                $query->where($where);
            }

            //特殊条件格式额外处理
            foreach ($whereInConditions as $condition) {
                if (empty($condition[2])) {
                    continue;
                }
                $value = Arr::normalize($condition[2]);
                $query->whereIn($condition[0], $value);
            }
        }
        // 应用字段选择
        if ($field !== '*') {
            $query->selectRaw($field); // 在这里应用 selectRaw
        }
        // 应用分页和其他查询条件
        if ($page > 0 && $limit > 0) {
            $query->paginate($limit, ['*'], 'page', $page);
        }
        if ($order !== '') {
            $query->orderByRaw($order);
        }
        if (!empty($with)) {
            $query->with($with);
        }
        return $query; // 返回查询构建器
    }

    /**
     * 获取条数
     *
     * @param array $where
     *
     * @return int
     * @throws \Exception
     */
    public function getCount(array $where): int
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * 计算符合条件的唯一记录数量
     *
     * @param array  $where
     * @param string $field
     * @param bool   $search
     *
     * @return int
     * @throws \Exception
     */
    public function getDistinctCount(array $where, string $field, bool $search = true): int
    {
        // 构建查询
        $query = $this->getModel();
        // 应用搜索条件
        if ($search) {
            $query = $this->search($query, $where);
        } else {
            $query = $query->where($where);
        }
        // 获取唯一计数
        return $query->distinct()->count($field);
    }

    /**
     * 获取模型
     *
     * @return mixed
     * @throws \Exception
     */
    public function getModel(): mixed
    {
        try {
            $className = $this->setModel();
            if (is_object($className)) {
                return $className;
            }
            if (!class_exists($className)) {
                throw new \Exception($className . ' 不是一个有效的模型类');
            }
            return new $className();
        } catch (\Throwable $e) {
            throw new \Exception($className . ' 未知模型: ' . $e->getMessage());
        }
    }

    /**
     * 获取模型主键
     *
     * @return string
     * @throws \Exception
     */
    public function getPk(): string
    {
        return $this->getModel()->getKeyName();
    }

    /**
     * 获取表名
     *
     * @return string
     * @throws \ReflectionException|\Exception
     */
    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    /**
     * 获取一条数据
     *
     * @param            $id
     * @param array|null $field
     * @param array|null $with
     * @param string     $order
     * @param array|null $withoutScopes
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function get($id, ?array $field = null, ?array $with = [], string $order = '', ?array $withoutScopes = null): ?\Illuminate\Database\Eloquent\Model
    {
        $where = is_array($id) ? $id : [$this->getPk() => $id];
        $query = $this->getModel()->where($where);
        $this->applyScopeRemoval($query, $withoutScopes);
        // 添加关联加载
        if (!empty($with)) {
            $query->with($with);
        }
        // 添加排序条件
        if ($order !== '') {
            $query->orderByRaw($order);
        }
        return $query->select($field ?? ['*'])->first();
    }

    /**
     * 查询一条数据是否存在
     *
     * @param        $map
     * @param string $field
     *
     * @return bool
     * @throws \Exception
     */
    public function be($map, string $field = ''): bool
    {
        // 如果 $map 不是数组且 $field 为空，使用主键
        if (!is_array($map) && empty($field)) {
            $field = $this->getPk();
        }

        // 如果 $map 不是数组，将其转换为数组
        $map = !is_array($map) ? [$field => $map] : $map;

        // 使用 Eloquent 查询构建器检查记录是否存在
        return $this->getModel()->where($map)->exists();
    }

    /**
     * 根据条件获取一条数据
     *
     * @param array       $where
     * @param string|null $field
     * @param array       $with
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getOne(array $where, ?string $field = '*', array $with = []): ?\Illuminate\Database\Eloquent\Model
    {
        // 将字段字符串转换为数组
        $fieldArray = $field === '*' ? ['*'] : explode(',', $field);
        // 使用 Eloquent 查询构建器获取一条数据
        return $this->getModel()->with($with)->where($where)->select($fieldArray)->first();
    }

    /**
     * 获取某字段的值
     *
     * @param             $where
     * @param string|null $field
     *
     * @return mixed
     * @throws \Exception
     */
    public function value($where, ?string $field = null): mixed
    {
        $pk    = $this->getPk(); // 获取主键
        $query = $this->getModel()->where($this->setWhere($where)); // 设置查询条件

        return $query->value($field ?? $pk); // 返回指定字段的值，默认为主键
    }

    /**
     * 获取某个字段数组
     *
     * @param array  $where
     * @param string $field
     * @param string $key
     *
     * @return array
     * @throws \ReflectionException|\Exception
     */
    public function getColumn(array $where, string $field, string $key = ''): array
    {
        // 使用 Eloquent 查询构建器获取字段数组
        $query = $this->getModel()->query();

        $whereInConditions = [];
        foreach ($where as $k => $condition) {
            if (is_array($condition) && count($condition) === 3 && ($condition[1] === 'in' || $condition[1] === 'IN')) {
                $whereInConditions[] = $condition;
                unset($where[$k]);//移除分离后的条件
            }
        }
        //普通条件格式直接传入构建
        if (!empty($where)) {
            $query->where($where);
        }

        //特殊条件格式额外处理
        foreach ($whereInConditions as $condition) {
            if (empty($condition[2])) {
                continue;
            }
            $value = Arr::normalize($condition[2]);
            $query->whereIn($condition[0], $value);
        }

        // 如果指定了键，则使用 keyBy 方法
        if ($key) {
            return $query->pluck($field, $key)->toArray();
        }

        // 否则，直接获取字段数组
        return $query->pluck($field)->toArray();
    }

    /**
     * 删除
     *
     * @param array|int|string $id
     * @param string|null      $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete(array|int|string $id, ?string $key = null): int
    {
        try {
            $query = $this->getModel()->query();
            if (is_array($id)) {
                if (array_is_list($id)) {
                    //主键列表批量删除
                    return $this->getModel()->destroy($id)->count();
                } else {
                    foreach ($id as $field => $value) {
                        $query->where($field, $value);
                    }
                }
            } else {
                $query->where($key ?: $this->getPk(), $id);
            }
            $models = $query->get();
            foreach ($models as $model) {
                $model->delete();//触发事件
            }
            return $models->count();
        } catch (\Exception $e) {
            throw new \Exception("删除失败:" . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 删除记录
     *
     * @param mixed $id
     * @param bool  $force
     *
     * @return bool
     * @throws \Exception
     */
    public function destroy(mixed $id, bool $force = false): bool
    {
        // 使用 Eloquent 的 destroy 方法删除记录
        return $this->getModel()->destroy($id, $force) > 0;
    }

    /**
     * 更新
     *
     * @param string|int|array $id
     * @param array            $data
     * @param string|null      $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function update(string|int|array $id, array $data, ?string $key = null): mixed
    {
        $where             = is_array($id) ? $id : [is_null($key) ? $this->getPk() : $key => $id];
        $query             = $this->getModel()->query();
        $whereInConditions = [];
        $whereInKeys       = [];

        // 分离 IN 条件
        foreach ($where as $key => $condition) {
            if (!is_array($condition)) {
                //条件不是数组直接跳过
                continue;
            }
            if ($this->isInCondition($condition)) {
                $whereInKeys[]       = $key;
                $whereInConditions[] = $condition;
            }
        }

        // 移除已处理的 IN 条件
        foreach ($whereInKeys as $key) {
            unset($where[$key]);
        }

        // 处理普通条件
        if (!empty($where)) {
            $query->where($where);
        }

        // 处理 IN 条件
        foreach ($whereInConditions as $condition) {
            list($column, $operator, $value) = $condition;
            $normalizedValue = is_array($value) ? $value : [$value];
            $query->where(function ($q) use ($column, $operator, $normalizedValue) {
                $q->whereIn($column, $normalizedValue);
            });
        }

        return $query->update($data);
    }

    /**
     * setWhere
     *
     * @param             $where
     * @param string|null $key
     *
     * @return array
     * @throws \Exception
     */
    protected function setWhere($where, ?string $key = null): array
    {
        // 如果 $where 不是数组，则构建数组
        if (!is_array($where)) {
            $where = [is_null($key) ? $this->getPk() : $key => $where];
        }
        return $where;
    }

    /**
     * 批量更新
     *
     * @param array       $ids
     * @param array       $data
     * @param string|null $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function batchUpdate(array $ids, array $data, ?string $key = null): bool
    {
        return $this->getModel()->whereIn(is_null($key) ? $this->getPk() : $key, $ids)->update($data);
    }

    /**
     * 保存返回模型
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function save(array $data): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->getModel()->create($data);
    }

    /**
     * 批量插入
     *
     * @param array $data
     *
     * @return bool
     */
    public function saveAll(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        try {
            $models = $this->getModel()->newCollection();

            foreach ($data as $item) {
                $models->push($this->getModel()->newInstance($item));
            }

            $savedAll = true;
            $models->each(function ($model) use (&$savedAll) {
                if (!$model->save()) {
                    $savedAll = false;
                }
            });

            return $savedAll;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取某字段内的值
     *
     * @param             $value
     * @param string      $field
     * @param string|null $valueKey
     * @param array|null  $where
     *
     * @return mixed
     * @throws \Exception
     */
    public function getFieldValue($value, string $field, ?string $valueKey = null, ?array $where = []): mixed
    {
        // 如果提供了 $valueKey，则构建查询条件
        if ($valueKey) {
            $where[$valueKey] = $value; // 将 valueKey 和 value 加入条件
        } else {
            $where[$this->getPk()] = $value; // 默认使用主键作为条件
        }
        // 使用 Eloquent 查询构建器获取字段值
        return $this->getModel()->where($where)->value($field);
    }

    /**
     * 获取搜索器和搜索条件key,以及不在搜索器的条件数组
     *
     * @param array $where
     *
     * @return array[]
     * @throws \ReflectionException
     */
    private function getSearchData(array $where): array
    {
        $with       = [];
        $withValues = []; // 用于存储与 $with 对应的值
        $otherWhere = [];
        $model      = $this->getModel();
        $responses  = new \ReflectionClass($model);

        foreach ($where as $key => $value) {
            $method = 'scope' . self::studly($key);
            if ($responses->hasMethod($method)) {
                $with[]           = $key; // 将搜索器方法的键加入 $with
                $withValues[$key] = $value; // 将对应的值存储到 $withValues
            } else {
                // 过滤不在搜索器中的条件
                if (!in_array($key, ['timeKey', 'store_stock', 'integral_time'])) {
                    if (!is_array($value)) {
                        $otherWhere[] = [$key, '=', $value]; // 单个条件
                    } elseif (count($value) === 3) {
                        $otherWhere[] = $value; // 复杂条件
                    }
                }
            }
        }
        return [$with, $withValues, $otherWhere]; // 返回 $with, $withValues 和 $otherWhere
    }

    /**
     * 根据搜索器获取内容
     *
     * @param array $where
     * @param bool  $search
     *
     * @return \Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    protected function withSearchSelect(array $where, bool $search): mixed
    {
        [$with, $withValues, $otherWhere] = $this->getSearchData($where);
        $query = $this->getModel()->query();
        foreach ($with as $item) {
            $func = self::studly($item);
            if (method_exists($this->getModel(), 'scope' . $func)) {
                $value = $withValues[$item] ?? null;
                if ($value !== null) {
                    $query->$func($value);
                }
            }
        }
        $filteredWhere = $this->filterWhere($otherWhere);
        if (!empty($filteredWhere)) {
            $query->where($filteredWhere);
        }
        return $query; // 返回查询构建器
    }

    /**
     * 过滤数据表中不存在的字段
     *
     * @param array $where
     *
     * @return array
     * @throws \Exception
     */
    protected function filterWhere(array $where = []): array
    {
        $fields = $this->getModel()->getFields(); // 获取模型的可填充字段
        foreach ($where as $key => $item) {
            // 检查键是否在可填充字段中
            if (!in_array($key, $fields)) {
                unset($where[$key]); // 过滤掉不存在的字段
            }
        }
        return $where; // 返回过滤后的条件
    }

    /**
     * 搜索
     *
     * @param array $where
     * @param bool  $search
     *
     * @return mixed
     * @throws \Exception
     */
    public function search(array $where = [], bool $search = true): mixed
    {
        if ($where) {
            return $this->withSearchSelect($where, $search); // 返回查询构建器
        } else {
            return $this->getModel(); // 返回模型实例
        }
    }

    /**
     * 求和
     *
     * @param array  $where
     * @param string $field
     * @param bool   $search
     *
     * @return float
     * @throws \Exception
     */
    public function sum(array $where, string $field, bool $search = false): float
    {
        // 构建查询
        $query = $this->getModel();
        // 应用搜索条件
        if ($search) {
            $query = $this->search($query, $where);
        } else {
            $query = $query->where($where);
        }
        // 计算总和并返回
        return (float)$query->sum($field);
    }

    /**
     * 高精度加法（修正精度问题）
     *
     * @param mixed $key 主键值或条件值
     * @param string $incField 要增加的字段
     * @param string $inc 增加的值
     * @param string|null $keyField 条件字段名，默认为'id'
     * @param int $acc 精度（小数位数）
     *
     * @return bool
     * @throws \Exception
     */
    public function bcInc(mixed $key, string $incField, string $inc, string $keyField = null, int $acc = 2): bool
    {
        // 获取模型实例
        $model = $this->getModel();
        // 构建查询条件
        $query = $keyField ? $model->where($keyField, $key) : $model->where('id', $key);
        // 执行增量操作，使用合适的精度 DECIMAL(10, $acc)
        return $query->update([$incField => Db::raw("COALESCE($incField, 0) + CAST($inc AS DECIMAL(10, $acc))")]) > 0;
    }

    /**
     * 高精度 减法
     *
     * @param             $key
     * @param string      $decField
     * @param string      $dec
     * @param string|null $keyField
     * @param int         $acc
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2): bool
    {
        return $this->bc($key, $decField, $dec, $keyField, 2, $acc);
    }

    /**
     * 高精度计算并保存
     *
     * @param             $key
     * @param string      $field
     * @param string      $value
     * @param string|null $keyField
     * @param int         $type
     * @param int         $acc
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function bc($key, string $field, string $value, string $keyField = null, int $type = 1, int $acc = 2): bool
    {
        // 获取记录
        $result = $keyField === null ? $this->get($key) : $this->getOne([$keyField => $key]);
        if (!$result) return false;
        $newValue = 0;
        if ($type === 1) {
            // 加法
            $newValue = bcadd($result->{$field}, $value, $acc);
        } elseif ($type === 2) {
            // 减法
            if ($result->{$field} < $value) return false; // 检查是否足够减去
            $newValue = bcsub($result->{$field}, $value, $acc);
        }
        // 更新字段
        $result->{$field} = $newValue;
        // 保存更新
        return $result->save();
    }

    /**
     * 减库存加销量
     *
     * @param array  $where
     * @param int    $num
     * @param string $stock
     * @param string $sales
     *
     * @return bool
     * @throws \Exception
     */
    public function decStockIncSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales'): bool
    {
        $product = $this->getModel()->where($where)->first();
        if ($product) {
            return $this->getModel()->where($where)->decrement($stock, $num)->increment($sales, $num);
        }
        return false;
    }

    /**
     * 加库存减销量
     *
     * @param array  $where
     * @param int    $num
     * @param string $stock
     * @param string $sales
     *
     * @return bool
     * @throws \Exception
     */
    public function incStockDecSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales'): bool
    {
        $product = $this->getModel()->where($where)->first();
        if ($product) {
            return $this->getModel()->where($where)->increment($stock, $num)->decrement($sales, $num);
        }
        return true;
    }

    /**
     * 获取条件数据中的某个值的最大值
     *
     * @param array  $where
     * @param string $field
     *
     * @return mixed
     * @throws \Exception
     */
    public function getMax(array $where = [], string $field = ''): mixed
    {
        return $this->getModel()->where($where)->max($field);
    }

    /**
     * 获取条件数据中的某个值的最小值
     *
     * @param array  $where
     * @param string $field
     *
     * @return mixed
     * @throws \Exception
     */
    public function getMin(array $where = [], string $field = ''): mixed
    {
        return $this->getModel()->where($where)->min($field);
    }

    private function studly(string $string): string
    {
        // 用下划线分隔单词
        $words = preg_split('/[\s_]+/', $string);
        // 将每个单词的首字母大写并连接
        $studlyCase = array_map('ucfirst', $words);
        // 返回连接后的结果
        return implode('', $studlyCase);
    }

    /**
     * 执行作用域移除操作
     *
     * @param            $query
     * @param array|null $scopes
     */
    protected function applyScopeRemoval($query, ?array $scopes): void
    {
        if (empty($scopes)) return;
        foreach ($scopes as $scope) {
            // 全局作用域移除
            if (is_string($scope) && class_exists($scope)) {
                $query->withoutGlobalScope($scope);
            } // 本地作用域移除
            elseif (is_string($scope)) {
                $query->withoutNamedScope($scope);
            } // 闭包动态移除
            elseif ($scope instanceof \Closure) {
                $scope($query);
            }
        }
    }

    /**
     * 是否in条件
     *
     * @param array $condition
     *
     * @return bool
     */
    protected function isInCondition(array $condition): bool
    {
        return count($condition) === 3
            && in_array(strtolower($condition[1] ?? ''), ['in', 'not in'], true);
    }
}

