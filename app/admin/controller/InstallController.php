<?php

namespace app\admin\controller;

use madong\utils\Json;
use madong\utils\JwtAuth;
use madong\utils\Snowflake;
use support\Container;
use support\Request;
use think\facade\Db;

class InstallController extends Base
{

    /**
     * 无需登录初始化
     */
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
        $is_install_file      = base_path() . '/install.lock';
        $database_config_file = base_path() . '/config/thinkorm1.php';
        clearstatcache();
        // 1.0检查是否已安装
        if (is_file($is_install_file)) {
            return Json::fail('管理后台已经安装！如需重新安装，请删除该根目录下的install.lock文件并重启: ');
        }

        // 2.0获取请求中的数据库连接信息
        $host            = $request->post('host');
        $database        = $request->post('database'); // 需要创建的数据库名
        $user            = $request->post('user');
        $password        = $request->post('password');
        $port            = (int)$request->post('port') ?: 3306;
        $overwrite       = $request->post('overwrite');//是否强制覆盖
        $config          = [
            'type'     => 'mysql',
            'hostname' => $host,
            'hostport' => $port,
            'username' => $user,
            'password' => $password,
            'database' => '',
            'charset'  => 'utf8mb4',
            'prefix'   => 'ma_',
            'debug'    => true,
        ];
        $database_config = [
            'default'     => 'mysql',
            'connections' => [
                'mysql' => $config,
            ],
        ];

        // 3.0数据库配置
        Db::setConfig($database_config);
        $db = Db::connect('mysql');

        // 4.0链接数据库
        try {
            $db = Db::connect('mysql');
            $db->execute("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $db->execute("USE `$database`");
            $tables = $db->query("SHOW TABLES");

        } catch (\Exception $e) {
            return Json::fail('数据库连接或创建失败: ' . $e->getMessage());
        }

        // 5.0预处理数据库表名
        $tables_to_install = [
//            'ma_system_menu',
//            'ma_system_user_role',
//            'ma_system_config',
//            'ma_system_crontab',
//            'ma_system_crontab_log',
//            'ma_system_dept',
//            'ma_system_dept_leader',
//            'ma_system_dict',
//            'ma_system_dict_item',
//            'ma_system_login_log',
//            'ma_system_menu',
//            'ma_system_operate_log',
//            'ma_system_post',
//            'ma_system_role',
//            'ma_system_role_dept',
//            'ma_system_role_menu',
//            'ma_system_upload',
//            'ma_system_user',
//            'ma_system_user_post',
//            'ma_system_user_role',
        ];

        // 6.0如果需要覆盖，删除冲突表
        $tables_exist = [];
        foreach ($tables as $table) {
            $tables_exist[] = current($table);
        }
        $tables_conflict = array_intersect($tables_to_install, $tables_exist);
        if (!$overwrite) {
            if ($tables_conflict) {
                // 6.1存在并且包含提示是否覆盖
                return Json::fail('以下表' . implode(',', $tables_conflict) . '已经存在，如需覆盖请选择强制覆盖');
            }
        } else {
            // 6.2移除重复的表
            foreach ($tables_conflict as $table) {
                $db->execute("DROP TABLE `$table`"); // 使用 execute() 方法
            }
        }

        // 7.0执行安装 SQL
        $sql_file = base_path() . '/install.sql';
        if (!is_file($sql_file)) {
            return Json::fail('数据库SQL文件不存在');
        }
        $sql_query = file_get_contents($sql_file);
        $sql_query = $this->removeComments($sql_query);
        $sql_query = $this->splitSqlFile($sql_query, ';');
        // 使用事务
        foreach ($sql_query as $sql) {
            $db->execute($sql);
        }

        // 导入菜单
        $menus = include base_path() . '/config/menu.php';
        $db->startTrans();
        for ($i = 1; $i <= 10; $i++) {
            $res = $this->generateSnowflakeID();
            echo "数字: $res\n";
        }
        eixt();
//        try {
//            $this->importMenu($menus, $db);
//            $db->commit();
//        } catch (\Exception $e) {
//            $db->rollback();
//            return Json::fail('导入菜单失败: ' . $e->getMessage());
//        }

        // 写入数据库配置文件
        $config['database'] = $database;
        $this->writeDatabaseConfig($database_config_file, $config);
        // 尝试 reload
        if (function_exists('posix_kill')) {
            set_error_handler(function () {
            });
            posix_kill(posix_getppid(), SIGUSR1);
            restore_error_handler();
        }
        return Json::success('操作成功');
    }

    protected function addMenu(array $menu, $db, int|string $pid = 0): int|string
    {
        $tableName = 'ma_system_menu';
        $id        = $this->generateSnowflakeID(); // 生成 ID
        // 准备数据
        $data = [
            'id'          => $id,
            'title'       => $menu['title'],
            'app'         => $menu['app'],
            'code'        => $menu['code'],
            'icon'        => $menu['icon'],
            'sort'        => $menu['sort'],
            'type'        => $menu['type'],
            'is_show'     => $menu['is_show'],
            'is_link'     => $menu['is_link'],
            'is_cache'    => $menu['is_cache'],
            'path'        => $menu['path'],
            'component'   => $menu['component'],
            'redirect'    => $menu['redirect'],
            'pid'         => $pid, // 关联父 ID
            'create_by'   => 1,
            'update_by'   => 1,
            'create_time' => time(),
            'update_time' => time(),
        ];

        // 插入数据并返回新插入的 ID
        $db->table($tableName)->insert($data);
        return $id; // 返回生成的 ID
    }

    protected function importMenu(array $menu_tree, $db, int|string $pid = 0)
    {
        foreach ($menu_tree as $menu) {
            // 添加当前菜单并获取当前菜单的 ID
            $currentId = $this->addMenu($menu, $db, $pid);

            // 处理子菜单
            $children = $menu['children'] ?? [];
            if (!empty($children)) {
                // 递归调用处理子菜单，传递当前菜单的 ID 作为父 ID
                $this->importMenu($children, $db, $currentId);
            }
        }
    }

    /**
     * 设置管理员
     *
     * @param \support\Request $request
     *
     * @return \support\Response
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
        $config_file = base_path() . '/config/thinkorm1.php';
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
        Db::setConfig($database_config);
        $db = Db::connect('mysql');
        // 检查是否已经存在管理员
        if ($db->table('ma_system_user')->count() > 0) {
            return Json::fail('后台已经安装完毕，无法通过此页面创建管理员');
        }

        // 插入管理员数据
        $data = [
            'id'          => (int)1,
            'user_name'   => $username,
            'password'    => JwtAuth::passwordHash($password),
            'real_name'   => '超级管理员',
            'is_super'    => 1,//超级管理员标识
            'create_time' => time(),
            'update_time' => time(),
        ];
        $db->table('ma_system_user')->insertGetId($data);
        $request->session()->flush();
        $is_install_file = base_path() . '/install.lock';
        file_put_contents($is_install_file, '');
        return Json::success('操作成功');
    }

    /**
     * 写入数据库配置文件
     *
     * @param string $file
     * @param array  $config
     */
    protected function writeDatabaseConfig(string $file, array $config)
    {
        $config_content = <<<EOF
                                <?php
                                    return  [
                                        'default' => 'mysql',
                                        'connections' => [
                                            'mysql' => [                            
                                                    // 数据库类型
                                                    'type' => 'mysql',
                                                    // 服务器地址
                                                    'hostname' => '{$config['hostname']}',
                                                    // 数据库名
                                                    'database' => '{$config['database']}',
                                                    // 数据库用户名
                                                    'username' => '{$config['username']}',
                                                    // 数据库密码
                                                    'password' => '{$config['password']}',
                                                    // 数据库连接端口
                                                    'hostport' => '{$config['hostport']}',
                                                    // 数据库连接参数
                                                    'params' => [
                                                        // 连接超时3秒
                                                        \PDO::ATTR_TIMEOUT => 3,
                                                    ],
                                                    // 数据库编码默认采用utf8
                                                    'charset' => 'utf8',
                                                    // 数据库表前缀
                                                    'prefix' => 'ma_',
                                                    // 断线重连
                                                    'break_reconnect' => true,
                                                    // 自定义分页类
                                                    'bootstrap' =>  ''                                                                    
                                            ],
                                        ],
                                    ];
                                EOF;
        file_put_contents($file, $config_content);
    }

    protected function removeComments($sql)
    {
        // 移除单行和多行注释
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // 移除 /* ... */
        // 移除 -- ...
        return preg_replace('/--.*?(\r?\n|$)/', '', $sql);
    }

    protected function splitSqlFile($sql, $delimiter): array
    {
        // 根据分隔符分割 SQL 语句
        return array_filter(array_map('trim', explode($delimiter, $sql)));
    }

    /**
     * 生成雪花ID
     *
     * @return int
     */
    private function generateSnowflakeID(): int
    {
        $snowflake = new Snowflake(1, 1);
        return $snowflake->nextId();
//        return random_int(1000000000000, 9999999999999); // 生成一个 13 位随机数
    }

}
