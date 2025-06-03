<?php

namespace app\admin\controller;

use madong\services\db\DataImporterService;
use madong\utils\Json;
use madong\utils\JwtAuth;
use support\Request;
use Webman\RedisQueue\Client;

/**
 * @author Mr.April
 * @since  1.0
 */
class InstallController extends Base
{

    public function initialize(): void
    {
    }

    public function index(Request $request): \support\Response
    {
        $is_install_file = base_path() . '/install.lock';
        if (is_file($is_install_file)) {
            return Json::fail('管理后台已经安装！如需重新安装，请删除该根目录下的install.lock文件并重启: ', []);
        }
        return Json::success('ok', []);
    }

    /**
     * 设置数据库
     *
     * @param Request $request
     *
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function step1(Request $request): \support\Response
    {
        try {

            $is_install_file      = base_path() . '/install.lock';
            $database_config_file = base_path() . '/config/database.php';
            clearstatcache();
            // 1.0检查是否已安装
            if (is_file($is_install_file)) {
                return Json::fail('管理后台已经安装！如需重新安装，请删除该根目录下的install.lock文件并重启: ');
            }
            // 2.0获取请求中的数据库连接信息
            $host      = $request->post('host');
            $database  = $request->post('database');
            $user      = $request->post('user');
            $password  = $request->post('password');
            $port      = (int)$request->post('port') ?: 3306;
            $overwrite = $request->post('overwrite', 'off');
            $overwrite = $overwrite == 'on';

            $dataImporterService = new DataImporterService();
            $pdo                 = $dataImporterService->getPdo($host, $user, $password, $port);
            // 关闭自动提交 使用手动事务提交
            $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
            $tablesToInstall = [
                'ma_system_menu',
                'ma_system_user_role',
                'ma_system_config',
                'ma_system_crontab',
                'ma_system_crontab_log',
                'ma_system_dept',
                'ma_system_dept_leader',
                'ma_system_dict',
                'ma_system_dict_item',
                'ma_system_login_log',
                'ma_system_menu',
                'ma_system_operate_log',
                'ma_system_post',
                'ma_system_role',
                'ma_system_role_dept',
                'ma_system_role_menu',
                'ma_system_upload',
                'ma_system_user',
                'ma_system_user_post',
                'ma_system_user_role',
            ];
            $pdo->beginTransaction();
            //1.0 安装数据表
            $dataImporterService->installDatabaseTables($pdo, $tablesToInstall, $database, $overwrite);
            //2.0 导入数据表数据
            //2.1 导入菜单
            $menus = include base_path() . '/scripts/config/menu.php';
            $field = ['pid', 'title', 'app', 'code', 'icon', 'sort', 'type', 'is_show', 'is_link', 'is_cache', 'path', 'component', 'redirect'];
            $dataImporterService->importTreeData($pdo, 'ma_system_menu', $field, $menus);
            //2.2 导入字典
            $dict   = include base_path() . '/scripts/config/dict.php';
            $field1 = ['group_code', 'name', 'code', 'sort', 'data_type', 'description', 'enabled', 'created_by', 'updated_by'];
            $field2 = ['label', 'value', 'code', 'sort', 'enabled', 'created_by', 'updated_by', 'remark'];
            $dataImporterService->importWithRelated($pdo, 'ma_system_dict', $field1, 'ma_system_dict_item', $field2, $dict, ['pidKey' => 'dict_id']);
            //2.3 导入初始用户
//            $userInfo  = include base_path() . '/scripts/config/user.php';
//            $field = ['id', 'user_name', 'real_name', 'nick_name', 'password', 'is_super', 'mobile_phone', 'email', 'avatar', 'signed', 'dashboard', 'dept_id', 'enabled', 'login_ip', 'login_time', 'backend_setting', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'sex', 'remark', 'birthday', 'tel', 'is_locked'];
//            $dataImporterService->importData($pdo, 'ma_system_user', $field, $userInfo);
            //2.4 导入系统配置数据
            $configuration = include base_path() . '/scripts/config/configuration.php';
            $field         = ["id", "group_code", "code", "name", "content", "is_sys", "enabled", "created_at", "created_by", "updated_at", "updated_by", "deleted_at", "remark"];
            $dataImporterService->importData($pdo, 'ma_system_config', $field, $configuration);
            //2.5 导入默认租户数据
            $tenant = include base_path() . '/scripts/config/tenant.php';
            $field  = ["id", "tenant_id", "package_id", "contact_user_name", "contact_phone", "company_name", "license_number", "address", "intro", "domain", "account_count", "enabled", "deleted_at", "created_dept", "created_by", "created_at", "expired_at", "remark", "updated_by", "updated_at", "is_default"];
            $dataImporterService->importData($pdo, 'ma_system_tenant', $field, $tenant);

            // 3.0 提交pdo 数据
            $pdo->commit();

            // 4.0 创建数据库配置-使用队列预防监听文件重启当前链接被重置可使用关闭监听文件改动实现
            $queue = 'write-database-config';
            $param = [
                'host'     => $host,
                'database' => $database,
                'username' => $user,
                'password' => $password,
                'port'     => $port,
            ];
            Client::send($queue, $param);

            return Json::success('操作成功');
        } catch (\Throwable $e) {
            return Json::fail('数据库连接或创建失败: ' . $e->getMessage());
        }
    }

    /**
     * 设置管理员
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Exception
     */
    public function step2(Request $request): \support\Response
    {
        $username         = $request->post('username');
        $password         = $request->post('password');
        $password_confirm = $request->post('password_confirm');

        // 检查密码是否一致
        if ($password != $password_confirm) {
            return Json::fail('两次密码不一致');
        }

        // 检查数据库配置文件是否存在
        $config_file = base_path() . '/config/database.php';
        // 检查配置文件是否存在
        if (!is_file($config_file)) {
            return Json::fail('请先完成第一步数据库配置');
        }

        // 引入配置文件
        $database_config = include $config_file;

        // 检查配置是否有效
        if (!is_array($database_config)) {
            return Json::fail('数据库配置文件格式错误');
        }

        $dataImporterService = new DataImporterService();

        $pdo = $dataImporterService->getPdo(
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.port'),
            config('database.connections.mysql.database')
        );
        // 关闭自动提交 使用手动事务提交
        $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        $pdo->beginTransaction();
        // 插入管理员数据
        $data  = [[
                      'id'          => (int)1,
                      'user_name'   => $username,
                      'password'    => JwtAuth::passwordHash($password),
                      'real_name'   => '超级管理员',
                      'is_super'    => 1,//超级管理员标识
                      'create_time' => time(),
                      'update_time' => time(),
                  ]];
        $field = ['id', 'user_name', 'real_name', 'nick_name', 'password', 'is_super', 'mobile_phone', 'email', 'avatar', 'signed', 'dashboard', 'dept_id', 'enabled', 'login_ip', 'login_time', 'backend_setting', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'sex', 'remark', 'birthday', 'tel', 'is_locked'];
        $dataImporterService->importData($pdo, 'ma_system_user', $field, $data);

        $is_install_file = base_path() . '/install.lock';
        file_put_contents($is_install_file, '');
        $pdo->commit();
        return Json::success('安装成功');
    }

    /**
     * 写入数据库配置文件
     *
     * @param string $file
     * @param array  $params
     */
    protected function writeDatabaseConfig(string $file, array $params)
    {
        $config_content = <<<EOF
                                <?php
                                    return  [
                                        'default' => 'mysql',
                                        'connections' => [
                                            'mysql' => [
                                                'driver'      => 'mysql',
                                                'host'        => '{$params['host']}',
                                                'port'        => '{$params['port']}',
                                                'database'    => '{$params['database']}',
                                                'username'    => '{$params['username']}',
                                                'password'    => '{$params['password']}',
                                                'unix_socket' => '',
                                                'charset'     => 'utf8',
                                                'collation'   => 'utf8_unicode_ci',
                                                'prefix'      => 'ma_',
                                                'strict'      => true,
                                                'engine'      => null,
                                                'pool' => [ 
                                                   // 连接池配置，仅支持swoole/swow驱动
                                                   'max_connections' => 5, // 最大连接数
                                                   'min_connections' => 1, // 最小连接数
                                                   'wait_timeout' => 3,    // 从连接池获取连接等待的最大时间，超时后会抛出异常
                                                   'idle_timeout' => 60,   // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
                                                   'heartbeat_interval' => 50, // 连接池心跳检测时间，单位秒，建议小于60秒
                                                ],
                                            ],
                                        ],
                                    ];
                                EOF;
        file_put_contents($file, $config_content);
    }

}


