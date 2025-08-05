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

namespace app\admin\controller;


use core\exception\handler\AdminException;
use core\excel\ExcelExportService;
use core\utils\Json;
use madong\helper\Arr;
use madong\helper\DateTime;
use madong\helper\Tree;
use support\Request;

/**
 * @author Mr.April
 * @since  1.0
 */
class Crud extends Base
{

    /**
     * @var array|string[]
     */
    public static array $prefixes = ['IN_', 'LIKE_', 'PREFIX_', 'EQ_', 'GT_', 'LT', 'GTE_', 'LTE_', 'NE_', 'BETWEEN_'];

    /**
     * 列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function index(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $total           = $this->service->getCount($where);
            $list            = $this->service->selectList($where, $field, $page, $limit, $order, [], false);
            return call_user_func([$this, $format_function], $list, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function select(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $data = $this->service->selectList($where, $field, 0, 99999, $order, [], true);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }

    /**
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function create(Request $request): \support\Response
    {
        try {
            $id = $request->get('id');
            throw new AdminException('表单不存在');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 插入数据
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function store(Request $request): \support\Response
    {
        try {
            $data = $this->insertInput($request);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('store')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            $model = $this->service->save($data);
            if (empty($model)) {
                throw new AdminException('插入失败');
            }
            $pk = $model->getPk();
            return Json::success('ok', [$pk => $model->getAttribute($pk)]);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }

    /**
     * 查询详情
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function show(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id');
            $data = $this->service->get($id);
            if (empty($data)) {
                throw new AdminException('数据未找到', 400);
            }
            return Json::success('ok', $data->toArray());
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    public function edit(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id');
            throw new AdminException('表单不存在');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function update(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id');
            $data = $this->insertInput($request);
            if (isset($this->validate) && $this->validate) {
                if (!$this->validate->scene('update')->check($data)) {
                    throw new \Exception($this->validate->getError());
                }
            }
            //路由模式兼容
            if (empty($id)) {
                $model      = $this->service->getModel();
                $primaryKey = $model->getKeyName();
                if (!array_key_exists($primaryKey, $data)) {
                    throw new \Exception('参数异常缺少参数:' . $primaryKey);
                }
                $id = $data[$primaryKey];
            }
            $this->service->update($id, $data);
            return Json::success('ok', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 更新状态
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function changeStatus(Request $request): \support\Response
    {
        try {
            $data       = $this->insertInput($request);
            $model      = $this->service->getModel();
            $primaryKey = $model->getKeyName();
            if (!array_key_exists($primaryKey, $data)) {
                throw new \Exception('参数异常缺少主键');
            }
            $targetModel = $model->findOrFail($data[$primaryKey]);
            if (empty($targetModel)) {
                throw new \Exception('资源不存在' . $primaryKey . '=', $data[$primaryKey]);
            }
            $targetModel->fill($data);
            if (!$targetModel->save()) {
                throw new \RuntimeException('数据保存失败');
            }
            return Json::success('ok', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * Excel导出
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function export(Request $request): \support\Response
    {
        try {
            $params = $request->all();
            $args   = (array)$params['query'] ?? [];
            $result = ExcelExportService::export($params, function ($chunkHandler) use ($args) {
                $model        = $this->service->getModel();
                $allow_column = $model->getFields();
                $query        = $model->query();
                $where        = $this->buildWhereConditions($args, $allow_column);
                if (!empty($where)) {
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
                //切片查询数据
                $query->chunk(1000, $chunkHandler);
            });
            return Json::success('导出成功', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 删除
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function destroy(Request $request): \support\Response
    {
        try {
            $id = $request->route->param('id'); // 获取路由地址 id从

            $data = $request->input('data', []);
            $data = !empty($id) && $id !== '0' ? $id : $data;
            if (empty($data)) {
                throw new AdminException('参数错误');
            }
            $result = $this->service->transaction(function () use ($data) {
                $data       = is_array($data) ? $data : explode(',', $data);
                $deletedIds = [];
                foreach ($data as $id) {
                    $item = $this->service->get($id);
                    if (!$item) {
                        continue; // 如果找不到项，跳过
                    }
                    $item->delete();
                    $primaryKey   = $item->getPk();
                    $deletedIds[] = $item->{$primaryKey};
                }
                return $deletedIds;
            });
            return Json::success('ok', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 数据恢复
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function recovery(Request $request): \support\Response
    {
        try {
            $id   = $request->route->param('id');
            $data = $request->input('data', []);
            $data = !empty($id) && $id !== '0' ? $id : $data;
            if (empty($data)) {
                throw new AdminException('参数错误');
            }
            $this->service->transaction(function () use ($data) {
                $data = is_array($data) ? $data : explode(',', $data);
                foreach ($data as $id) {
                    $this->service->update($id, ['delete' => null]);
                }
            });
            return Json::success('操作成功', []);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 从请求参数中构建WHERE查询条件
     *
     * @param array $params       请求参数
     * @param array $allowColumns 允许查询的字段白名单
     *
     * @return array 格式化后的WHERE条件数组
     */
    protected function buildWhereConditions(array $params, array $allowColumns): array
    {
        $where = [];

        foreach ($params as $column => $value) {
            // 解析条件前缀和实际字段名
            $prefix       = '';
            $actualColumn = $column;

            if (preg_match('/^([A-Z_]+)_(.*)$/', $column, $matches)) {
                $prefix       = $matches[1];
                $actualColumn = strtolower($matches[2]);
            }

            // 字段白名单验证
            if (!in_array($actualColumn, $allowColumns, true)) {
                continue;
            }

            // 构建不同条件的查询逻辑
            switch ($prefix) {
                case 'IN':
                    if (is_array($value)) {
                        $where[] = [$actualColumn, 'IN', $value];
                    }
                    break;

                case 'LIKE':
                    $where[] = [$actualColumn, 'LIKE', "%{$value}%"];
                    break;

                case 'GT':
                    $where[] = [$actualColumn, '>', $value];
                    break;

                case 'LT':
                    $where[] = [$actualColumn, '<', $value];
                    break;

                case 'GTE':
                    $where[] = [$actualColumn, '>=', $value];
                    break;

                case 'LTE':
                    $where[] = [$actualColumn, '<=', $value];
                    break;

                case 'NE':
                    $where[] = [$actualColumn, '!=', $value];
                    break;

                case 'BETWEEN':
                    if (is_array($value) && count($value) === 2) {
                        $start   = DateTime::dateTimeStringToTimestamp($value[0]);
                        $end     = DateTime::dateTimeStringToTimestamp($value[1]);
                        $where[] = [$actualColumn, '>', $start];
                        $where[] = [$actualColumn, '<', $end];
                    }
                    break;

                case 'EQ':
                default:
                    $where[] = [$actualColumn, '=', $value];
                    break;
            }
        }
        return $where;
    }

    protected function selectInput(Request $request): array
    {
        $field        = $request->input('field', '*');
        $sort         = $request->input('order', 'create_time');
        $format       = $request->input('format', 'normal');
        $limit        = (int)$request->input('limit', $format === 'tree' ? 10000 : 10);
        $limit        = $limit <= 0 ? 10 : $limit;
        $param        = $request->all();
        $page         = (int)$request->input('page');
        $page         = $page > 0 ? $page : 1;
        $model        = $this->service->getModel();
        $allow_column = $model->getFields();
        $parts        = explode(' ', $sort);
        $order        = '';
        if (in_array($parts[0], $allow_column)) {
            $rank  = $parts[1] ?? 'desc';
            $order = $parts[0] . ' ' . $rank;
        }

        if (!in_array($field, $allow_column)) {
            $field = '*';
        }
        $where = $this->buildWhereConditions($request->all(), $allow_column);
        return [$where, $format, $limit, $field, $order, $page];
    }

    /**
     * 插入前置方法
     *
     * @param Request $request
     *
     * @return array
     */
    protected function insertInput(Request $request): array
    {
        $data           = $this->inputFilter($request->all());
        $password_filed = 'password';
        if (isset($data[$password_filed])) {
            $data[$password_filed] = JwtAuth::passwordHash($data[$password_filed]);
        }
        return $data;
    }

    /**
     * 对用户输入表单过滤
     *
     * @param array $data
     * @param array $skipKeys
     *
     * @return array
     */
    protected function inputFilter(array $data, array $skipKeys = []): array
    {
        $model   = $this->service->getModel();
        $columns = $model->getFields();
        foreach ($data as $col => $item) {
            // 检查是否在跳过的键中，或者不在列中
            if (!in_array($col, $columns) && !in_array($col, $skipKeys)) {
                unset($data[$col]);
                continue;
            }
        }

        // 处理时间字段
        if (empty($data['created_at']) && !in_array('created_at', $skipKeys)) {
            unset($data['created_at']);
        }
        if (empty($data['updated_at']) && !in_array('updated_at', $skipKeys)) {
            unset($data['updated_at']);
        }
        return $data;
    }

    /**
     * 格式化树
     *
     * @param $items
     *
     * @return \support\Response
     */
    protected function formatTree($items): \support\Response
    {
        $format_items = [];
        foreach ($items as $item) {
            $format_items[] = [
                'name'  => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'id'    => $item->id,
                'pid'   => $item->pid,
            ];
        }
        $tree = new Tree($format_items);
        return Json::success('ok', $tree->getTree());
    }

    /**
     * 格式化表格树
     *
     * @param $data
     * @param $total
     *
     * @return \support\Response
     */
    protected function formatTableTree($data, $total): \support\Response
    {
        $tree  = new Tree($data->toArray());
        $items = $tree->getTree();
        return Json::success('ok', $items);
    }

    /**
     * 格式化下拉列表
     *
     * @param $items
     *
     * @return \support\Response
     */
    protected function formatSelect($items): \support\Response
    {
        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'label' => $item->title ?? $item->name ?? $item->real_name ?? $item->id,
                'value' => $item->id,
            ];
        }
        return Json::success('ok', $formatted_items);
    }

    /**
     * 通用格式化
     *
     * @param $items
     * @param $total
     *
     * @return \support\Response
     */
    protected function formatNormal($items, $total): \support\Response
    {
        return Json::success('ok', compact('items', 'total'));
    }

    public function dev(Request $request): \support\Response
    {
        return Json::fail('接口开发中');
    }

    protected function removePrefixes(array $input, array $prefixes = []): array
    {
        $output   = [];
        $prefixes = empty($prefixes) ? self::$prefixes : $prefixes;
        foreach ($input as $key => $value) {
            $found = false;
            foreach ($prefixes as $prefix) {
                if (str_starts_with($key, $prefix)) {
                    $output[substr($key, strlen($prefix))] = $value;
                    $found                                 = true;
                    break;
                }
            }
            if (!$found) {
                $output[$key] = $value;
            }
        }
        return $output;
    }

}
