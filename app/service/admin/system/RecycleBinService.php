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

namespace app\service\admin\system;

use app\dao\system\RecycleBinDao;
use app\model\system\RecycleBin;
use core\base\BaseService;
use core\exception\handler\AdminException;
use support\Db;

/**
 * 回收站服务
 *
 * @author Mr.April
 * @since  1.0
 */
class RecycleBinService extends BaseService
{
    public function __construct(RecycleBinDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 数据恢复
     *
     * @param int $recycleId
     *
     * @throws \Throwable
     */
    public function restoreRecycleBin(int $recycleId): void
    {
        $this->transaction(function () use ($recycleId) {
            $record = $this->dao->get($recycleId, null, [], '');
            if (empty($record)) {
                throw new AdminException("回收站记录不存在");
            }
            // 3. 动态恢复数据
            $this->restoreOriginalData($record);

            // 4. 删除回收站记录
            $record->delete();
        });
    }

    /**
     * 恢复原始数据
     */
    protected function restoreOriginalData(RecycleBin $record): void
    {
        $tableName    = $record->table_name;
        $config       = self::getTableConfig($tableName);
        $storage_mode = $config['storage_mode'];
        $connection   = config('database.default');
        $tableData    = json_decode($record->getData('data'), true);
        $columns      = $this->getTableColumns($tableName, $connection);
        $tableData    = array_intersect_key($tableData, array_flip($columns));
        Db::connection($connection)->table($tableName)->insert($tableData);
    }

    /**
     * 获取还原表的数据列
     *
     * @param string $tableName
     * @param string $connection
     *
     * @return array
     */
    private function getTableColumns(string $tableName, string $connection = ''): array
    {
        return Db::connection($connection)->getSchemaBuilder()->getColumnListing($tableName);
    }

    /**
     * 获取表配置（合并全局+表级配置）
     *
     * @param string $table
     *
     * @return array
     */
    // 获取合并配置
    public static function getTableConfig(string $table): array
    {
        return array_merge(
            config('recycle_bin.default'),
            config("recycle_bin.tables.{$table}", []),
            ['tenant' => config('recycle_bin.tenant')]
        );
    }

}
