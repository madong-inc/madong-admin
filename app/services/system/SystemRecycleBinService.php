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

namespace app\services\system;

use app\dao\system\SystemRecycleBinDao;
use madong\basic\BaseService;
use madong\exception\AdminException;
use support\Container;
use support\Db as LaravelDb;
use think\facade\Db as ThinkDb;

/**
 * 回收站服务
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemRecycleBinService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(SystemRecycleBinDao::class);
    }

    /**
     * 恢复删除数据
     *
     * @param string|int $id
     */
    public function restoreRecycleBin(string|int $id): void
    {
        try {
            $data = $this->dao->get($id);
            if (!empty($data)) {
                $this->transaction(function () use ($data) {
                    if (config('app.model_type', 'thinkORM') == 'thinkORM') {
                        $tablePrefix = $data->getData('table_prefix');
                        $tableName   = $data->getData('table_name');
                        $tableData   = json_decode($data->getData('data'), true);
                        $columns     = $this->getTableColumns($tableName, $tablePrefix);
                        $tableData   = array_intersect_key($tableData, array_flip($columns));
                        ThinkDb::name($tableName)->insert($tableData);
                    } else {
                        $tableName = $data->getData('table_name');
                        $tableData = json_decode($data->getData('data'), true);
                        $columns   = $this->getTableColumns($tableName);
                        $tableData = array_intersect_key($tableData, array_flip($columns));
                        LaravelDb::table($tableName)->insert($tableData);
                    }
                    $data->delete();
                });
            }
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 获取还原表的数据列
     *
     * @param string $tableName
     * @param string $tablePrefix
     *
     * @return array
     */
    private function getTableColumns(string $tableName, string $tablePrefix = ''): array
    {
        return LaravelDb::getSchemaBuilder()->getColumnListing($tableName);
    }
}
