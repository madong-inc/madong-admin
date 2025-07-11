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

namespace app\common\services\platform;

use app\common\dao\platform\DbSettingDao;
use madong\admin\abstract\BaseService;
use madong\admin\context\TenantContext;
use madong\admin\services\db\DataImporterService;
use support\Container;

class DbSettingService extends BaseService
{

    public function __construct()
    {
        $this->dao = Container::make(DbSettingDao::class);
    }

    /**
     * 删除账套
     *
     * @throws \Throwable
     */
    public function remove(string|int $id, bool $isLibrary = false): void
    {
        try {
            $this->transaction(function () use ($id, $isLibrary) {
                $result = $this->dao->get($id);
                if (!empty($result)) {
                    if ($result->database == TenantContext::getDatabaseConnection()) {
                        throw new \Exception(TenantContext::getDatabaseConnection() . '正在使用中');
                    }
                    $service       = new TenantService();
                    $includeTenant = $service->getModel()->query()->where('db_name', $result->database)->pluck('company_name')->toArray();
                    if (!empty($includeTenant)) {
                        throw new \Exception('不允许操作,数据源包含租户:【' . implode(',', $includeTenant) . '】');
                    }
                    $result->delete();
                    if ($isLibrary) {
                        $dataImporterService = new DataImporterService();
                        $pdo                 = $dataImporterService->getPdo($result->host, $result->username, $result->password, $result->port);
                        $database            = $result->database;
                        $smt                 = $pdo->query("show databases like '$database'");
                        //数据表存在移除操作
                        if (!empty($smt->fetchAll())) {
                            try {
                                $pdo->exec("DROP DATABASE IF EXISTS `$database`");
                            } catch (\PDOException $e) {
                                throw new \Exception($e->getMessage());
                            }
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 添加数据中心
     *
     * @throws \Exception
     */
    public function save(array $data): void
    {
        try {
            $this->transaction(function () use ($data) {
                try {
                    $database            = $data['database'];
                    $overwrite           = $data['overwrite'] ?? 0;
                    $dataImporterService = new DataImporterService();
                    $pdo                 = $dataImporterService->getPdo($data['host'], $data['username'], $data['password'], $data['port']);
                    // 关闭自动提交 使用手动事务提交
                    // $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
                    $tablesToInstall = [
                        'ma_cache',
                        'ma_mt_db_setting',
                        'ma_mt_tenant',
                        'ma_mt_tenant_package',
                        'ma_mt_tenant_session',
                        'ma_mt_tenant_subscription',
                        'ma_mt_tenant_subscription_menu',
                        'ma_sys_admin',
                        'ma_sys_admin_dept',
                        'ma_sys_admin_post',
                        'ma_sys_admin_role',
                        'ma_sys_admin_tenant',
                        'ma_sys_admin_tenant_dept',
                        'ma_sys_admin_tenant_post',
                        'ma_sys_admin_tenant_role',
                        'ma_sys_config',
                        'ma_sys_crontab',
                        'ma_sys_crontab_log',
                        'ma_sys_dept',
                        'ma_sys_dept_leader',
                        'ma_sys_dict',
                        'ma_sys_dict_item',
                        'ma_sys_login_log',
                        'ma_sys_menu',
                        'ma_sys_message',
                        'ma_sys_notice',
                        'ma_sys_operate_log',
                        'ma_sys_post',
                        'ma_sys_rate_limiter',
                        'ma_sys_rate_restrictions',
                        'ma_sys_recycle_bin',
                        'ma_sys_role',
                        'ma_sys_role_dept',
                        'ma_sys_role_menu',
                        'ma_sys_role_scope_dept',
                        'ma_sys_route',
                        'ma_sys_route_cate',
                        'ma_sys_upload',
                    ];
                    // $pdo->beginTransaction();
                    //1.0 安装数据表
                    $dataImporterService->installDatabaseTables($pdo, $tablesToInstall, $database, $overwrite);

                    //$pdo->commit();
                    $this->dao->save($data);
                } catch (\Throwable $e) {
                    throw new \Exception($e->getMessage());
                }
            });
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
