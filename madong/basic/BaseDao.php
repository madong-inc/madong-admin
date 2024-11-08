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

namespace madong\basic;

use madong\exception\ApiException;
use ReflectionClass;
use think\helper\Str;
use Webman\Container;

/**
 * Class BaseDao
 *
 * @package app\dao
 */
abstract class BaseDao
{
    /**
     * 当前表名别名
     *
     * @var string
     */
    protected string $alias;

    /**
     * join表别名
     *
     * @var string
     */
    protected string $joinAlis;

    /**
     * 获取当前模型
     *
     * @return string
     */
    abstract protected function setModel(): string;

    /**
     * 设置join链表模型
     *
     * @return string
     */
    protected function setJoinModel(): string
    {
    }

    /**
     * 读取数据条数
     *
     * @param array $where
     * @param bool  $search
     *
     * @return int
     * @throws \ReflectionException
     */
    public function count(array $where = [], bool $search = true)
    {
        return $this->search($where, $search)->count();
    }

    /**
     * 获取某些条件数据
     *
     * @param array  $where
     * @param string $field
     * @param int    $page
     * @param int    $limit
     * @param string $order
     * @param array  $with
     * @param bool   $search
     *
     * @return \think\Collection|null
     * @throws \ReflectionException
     */
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false): \think\Collection|null
    {
        return $this->selectModel($where, $field, $page, $limit, $order, $with, $search)->select();
    }

    /**
     * selectModel
     *
     * @param array  $where
     * @param string $field
     * @param int    $page
     * @param int    $limit
     * @param string $order
     * @param array  $with
     * @param bool   $search
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function selectModel(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false)
    {
        if ($search) {
            $model = $this->search($where);
        } else {
            $model = $this->getModel()->where($where);
        }
        return $model->field($field)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->when($order !== '', function ($query) use ($order) {
            $query->order($order);
        })->when($with, function ($query) use ($with) {
            $query->with($with);
        });
    }

    /**
     * 获取某些条件总数
     *
     * @param array $where
     *
     * @return int
     * @throws \ReflectionException
     */
    public function getCount(array $where): int
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * 获取某些条件去重总数
     *
     * @param array $where
     * @param       $field
     * @param bool  $search
     *
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException|\ReflectionException
     */
    public function getDistinctCount(array $where, $field, bool $search = true): mixed
    {
        if ($search) {
            return $this->search($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;
        } else {
            return $this->getModel()->where($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;
        }
    }

    /**
     * 获取模型
     *
     * @return \ingenstream\basic\BaseModel|null
     * @throws \ReflectionException
     */
    public function getModel():mixed
    {
        //优化兼容其他框架使用反射实例化
        $className = $this->setModel();
        if (class_exists($className)) {
            $reflectionClass = new ReflectionClass($className);
            return $reflectionClass->newInstance();
        } else {
            throw new ApiException($className . '未知模型');
        }
    }

    /**
     * 获取主键
     *
     * @return array|string
     * @throws \ReflectionException
     */
    protected function getPk()
    {
        return $this->getModel()->getPk();
    }

    /**
     * getTableName
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getTableName(): mixed
    {
        return $this->getModel()->getName();
    }

    /**
     * 获取一条数据
     *
     * @param            $id
     * @param array|null $field
     * @param array|null $with
     * @param string     $order
     *
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException|\ReflectionException
     */
    public function get($id, ?array $field = [], ?array $with = [], string $order = ''): mixed
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [$this->getPk() => $id];
        }
        return $this->getModel()->where($where)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->when($order !== '', function ($query) use ($order) {
            $query->order($order);
        })->field($field ?? ['*'])->find();
    }

    /**
     * 查询一条数据是否存在
     *
     * @param        $map
     * @param string $field
     *
     * @return bool 是否存在
     * @throws \ReflectionException
     */
    public function be($map, string $field = ''): bool
    {
        if (!is_array($map) && empty($field)) $field = $this->getPk();
        $map = !is_array($map) ? [$field => $map] : $map;
        return 0 < $this->getModel()->where($map)->count();
    }

    /**
     * 根据条件获取一条数据
     *
     * @param array       $where
     * @param string|null $field
     * @param array       $with
     *
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException|\ReflectionException
     */
    public function getOne(array $where, ?string $field = '*', array $with = []): Model|array|null
    {
        $field = explode(',', $field);
        return $this->get($where, $field, $with);
    }

    /**
     * 获取单个字段值
     *
     * @param             $where
     * @param string|null $field
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function value($where, ?string $field = ''): mixed
    {
        $pk = $this->getPk();
        return $this->search($this->setWhere($where))->value($field ?: $pk);
    }

    /**
     * 获取某个字段数组
     *
     * @param array  $where
     * @param string $field
     * @param string $key
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getColumn(array $where, string $field, string $key = ''): array
    {
        return $this->getModel()->where($where)->column($field, $key);
    }

    /**
     * 删除
     *
     * @param array|int|string $id
     * @param string|null      $key
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function delete(array|int|string $id, ?string $key = null): mixed
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }

        return $this->getModel()->where($where)->delete();
    }

    /**
     *删除记录
     *
     * @param mixed $id
     * @param bool  $force
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function destroy(mixed $id, bool $force = false): bool
    {
        return $this->getModel()->destroy($id, $force);
    }

    /**
     * 更新数据
     *
     * @param int|string|array $id
     * @param array            $data
     * @param string|null      $key
     *
     * @return BaseModel
     * @throws \ReflectionException
     */
    public function update(string|int|array $id, array $data, ?string $key = null): mixed
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::update($data, $where);
    }

    /**
     * setWhere
     *
     * @param             $where
     * @param string|null $key
     *
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected function setWhere($where, ?string $key = null): mixed
    {
        if (!is_array($where)) {
            $where = [is_null($key) ? $this->getPk() : $key => $where];
        }
        return $where;
    }

    /**
     * 批量更新数据
     *
     * @param array       $ids
     * @param array       $data
     * @param string|null $key
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function batchUpdate(array $ids, array $data, ?string $key = null): mixed
    {
        return $this->getModel()->whereIn(is_null($key) ? $this->getPk() : $key, $ids)->update($data);
    }

    /**
     * 插入数据
     *
     * @param array $data
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function save(array $data): mixed
    {
        return $this->getModel()::create($data);
    }

    /**
     * 插入数据
     *
     * @param array $data
     *
     * @return \think\Collection
     * @throws \Exception
     */
    public function saveAll(array $data): \think\Collection
    {
        return $this->getModel()->saveAll($data);
    }

    /**
     * 获取某个字段内的值
     *
     * @param                $value
     * @param string         $filed
     * @param string|null    $valueKey
     * @param array|string[] $where
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getFieldValue($value, string $filed, ?string $valueKey = '', ?array $where = []): mixed
    {
        return $this->getModel()->getFieldValue($value, $filed, $valueKey, $where);
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
        $otherWhere = [];
        $responses  = new \ReflectionClass($this->setModel());
        foreach ($where as $key => $value) {
            $method = 'search' . Str::studly($key) . 'Attr';
            if ($responses->hasMethod($method)) {
                $with[] = $key;
            } else {
                if (!in_array($key, ['timeKey', 'store_stock', 'integral_time'])) {
                    if (!is_array($value)) {
                        $otherWhere[] = [$key, '=', $value];
                    } else if (count($value) === 3) {
                        $otherWhere[] = $value;
                    }
                }
            }
        }
        return [$with, $otherWhere];
    }

    /**
     * 根据搜索器获取搜索内容
     * withSearchSelect
     *
     * @param $where
     * @param $search
     *
     * @return \think\db\Query|\BaseModel
     * @throws \ReflectionException
     */
    protected function withSearchSelect($where, $search): \think\db\Query|\BaseModel
    {
        [$with, $otherWhere] = $this->getSearchData($where);
        return $this->getModel()->withSearch($with, $where)->when($search, function ($query) use ($otherWhere) {
            $query->where($this->filterWhere($otherWhere));
        });
    }

    /**
     * 过滤数据表中不存在的where条件字段
     *
     * @param array $where
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function filterWhere(array $where = []): array
    {
        $fields = $this->getModel()->getTableFields();
        foreach ($where as $key => $item) {
            if (!in_array($item[0], $fields)) {
                unset($where[$key]);
            }
        }
        return $where;
    }

    /**
     *  搜索
     *
     * @param array $where
     * @param bool  $search
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function search(array $where = [], bool $search = true):mixed
    {
        if ($where) {
            return $this->withSearchSelect($where, $search);
        } else {
            return $this->getModel();
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
     * @throws \ReflectionException
     */
    public function sum(array $where, string $field, bool $search = false): float
    {
        if ($search) {
            return $this->search($where)->sum($field);
        } else {
            return $this->getModel()->where($where)->sum($field);
        }
    }

    /**
     * 高精度加法
     *
     * @param             $key
     * @param string      $incField
     * @param string      $inc
     * @param string|null $keyField
     * @param int         $acc
     *
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bcInc($key, string $incField, string $inc, string $keyField = null, int $acc = 2): bool
    {
        return $this->bc($key, $incField, $inc, $keyField, 1);
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2): bool
    {
        return $this->bc($key, $decField, $dec, $keyField, 2);
    }

    /**
     * 高精度计算并保存
     *
     * @param             $key
     * @param string      $incField
     * @param string      $inc
     * @param string|null $keyField
     * @param int         $type
     * @param int         $acc
     *
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bc($key, string $incField, string $inc, string $keyField = null, int $type = 1, int $acc = 2): bool
    {
        if ($keyField === null) {
            $result = $this->get($key);
        } else {
            $result = $this->getOne([$keyField => $key]);
        }
        if (!$result) return false;
        $new = 0;
        if ($type === 1) {
            $new = bcadd($result[$incField], $inc, $acc);
        } else if ($type === 2) {
            if ($result[$incField] < $inc) return false;
            $new = bcsub($result[$incField], $inc, $acc);
        }
        $result->{$incField} = $new;
        return false !== $result->save();
    }

    /**
     * 减库存加销量
     *
     * @param array  $where
     * @param int    $num
     * @param string $stock
     * @param string $sales
     *
     * @return false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function decStockIncSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales'): bool
    {
        $isQuota = false;
        if (isset($where['type']) && $where['type']) {
            $isQuota = true;
            if (count($where) == 2) {
                unset($where['type']);
            }
        }
        $field   = $isQuota ? 'stock,quota' : 'stock';
        $product = $this->getModel()->where($where)->field($field)->find();
        if ($product) {
            return $this->getModel()->where($where)->when($isQuota, function ($query) use ($num) {
                $query->dec('quota', $num);
            })->dec($stock, $num)->inc($sales, $num)->update();
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
     * @return mixed
     * @throws \ReflectionException
     */
    public function incStockDecSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales'): mixed
    {
        $isQuota = false;
        if (isset($where['type']) && $where['type']) {
            $isQuota = true;
            if (count($where) == 2) {
                unset($where['type']);
            }
        }
        $salesOne = $this->getModel()->where($where)->value($sales);
        if ($salesOne) {
            $salesNum = $num;
            if ($num > $salesOne) {
                $salesNum = $salesOne;
            }
            return $this->getModel()->where($where)->when($isQuota, function ($query) use ($num) {
                $query->inc('quota', $num);
            })->inc($stock, $num)->dec($sales, $salesNum)->update();
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public function getMin(array $where = [], string $field = ''): mixed
    {
        return $this->getModel()->where($where)->min($field);
    }
}
