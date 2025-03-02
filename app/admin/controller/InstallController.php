<?php

namespace app\admin\controller;

use madong\utils\Json;
use madong\utils\JwtAuth;
use madong\utils\Snowflake;
use support\Request;
use think\facade\Db;

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
        $is_install_file      = base_path() . '/install.lock';
        $database_config_file = base_path() . '/config/thinkorm.php';
        clearstatcache();
        // 1.0检查是否已安装
        if (is_file($is_install_file)) {
            return Json::fail('管理后台已经安装！如需重新安装，请删除该根目录下的install.lock文件并重启: ');
        }

        // 2.0获取请求中的数据库连接信息
        $host            = $request->post('host');
        $database        = $request->post('database');
        $user            = $request->post('user');
        $password        = $request->post('password');
        $port            = (int)$request->post('port') ?: 3306;
        $overwrite       = $request->post('overwrite');
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

        // 4.0链接数据库
        $db = Db::connect('mysql');

        $db->startTrans();
        try {
            $db = Db::connect('mysql');
            $db->execute("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $db->execute("USE `$database`");
            $tables = $db->query("SHOW TABLES");

            // 5.0预处理数据库表名
            $tables_to_install = [
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

            // 6.0如果需要覆盖，删除冲突表
            $tables_exist = [];
            foreach ($tables as $table) {
                $tables_exist[] = current($table);
            }
            $tables_conflict = array_intersect($tables_to_install, $tables_exist);
            if (!$overwrite) {
                if ($tables_conflict) {
                    // 6.1存在并且包含提示是否覆盖
                    throw new \Exception('以下表' . implode(',', $tables_conflict) . '已经存在，如需覆盖请选择强制覆盖');
                }
            } else {
                // 6.2移除重复的表
                foreach ($tables_conflict as $table) {
                    $db->execute("DROP TABLE `$table`"); // 使用 execute() 方法
                }
            }

            // 7.0执行安装 SQL
            $sql_file = base_path() . 'scripts/install.sql';
            if (!is_file($sql_file)) {
                throw new \Exception('数据库SQL文件不存在');
            }
            $sql_query = file_get_contents($sql_file);
            $sql_query = $this->removeComments($sql_query);
            $sql_query = $this->splitSqlFile($sql_query, ';');

            // 8.0创建表
            foreach ($sql_query as $sql) {
                $db->execute($sql);
            }

            // 9.0导入数据
            $menus         = include base_path() . '/madong/config/menu.php';
            $dicts         = include base_path() . '/madong/config/dict.php';
            $configuration = include base_path() . '/madong/config/configuration.php';

            // 9.1导入菜单
            $this->importMenu($menus, $db);
            // 9.2导入字典
            $this->importDict($dicts, $db);
            // 9.3导入系统配置
            $this->importConfiguration($configuration, $db);
            //更多添加



            $db->commit();
            // 1.0写入数据库配置文件
            $config['database'] = $database;
            $this->writeDatabaseConfig($database_config_file, $config);
            // reload
            if (function_exists('posix_kill')) {
                set_error_handler(function () {
                });
                posix_kill(posix_getppid(), SIGUSR1);
                restore_error_handler();
            }
            return Json::success('操作成功');
        } catch (\Throwable $e) {
            $db->rollback();
            return Json::fail('数据库连接或创建失败: ' . $e->getMessage());
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
        $config_file = base_path() . '/config/thinkorm.php';
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
     * 添加菜单
     *
     * @param array      $menu
     * @param            $db
     * @param int|string $pid
     * @param string     $tableName
     *
     * @return int|string
     */
    protected function addMenu(array $menu, $db, int|string $pid = 0, string $tableName = 'ma_system_menu'): int|string
    {
        $id = $this->generateSnowflakeID(); // 生成 ID
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
        $db->table($tableName)->insert($data);
        return $id;
    }

    /**
     * 导入菜单
     *
     * @param array      $menu_tree
     * @param            $db
     * @param int|string $pid
     */
    protected function importMenu(array $menu_tree, $db, int|string $pid = 0)
    {
        foreach ($menu_tree as $menu) {
            // 添加当前菜单并获取当前菜单的 ID
            $currentId = $this->addMenu($menu, $db, $pid);
            usleep(10);//延迟10微秒避免时间戳无变化id重复
            // 处理子菜单
            $children = $menu['children'] ?? [];
            if (!empty($children)) {
                // 递归调用处理子菜单，传递当前菜单的 ID 作为父 ID
                $this->importMenu($children, $db, $currentId);
            }
        }
    }

    /**
     * 添加字典
     *
     * @param array  $groupData
     * @param        $db
     * @param string $tableName
     *
     * @return int|string
     */
    protected function addDictGroup(array $groupData, $db, string $tableName = 'ma_system_dict'): int|string
    {
        $id = $this->generateSnowflakeID(); // 生成 ID
        // 准备数据
        $data = [
            'id'          => $id,
            'group_code'  => $groupData['group_code'],
            'name'        => $groupData['name'],
            'code'        => $groupData['code'],
            'sort'        => $groupData['sort'],
            'data_type'   => $groupData['data_type'],
            'description' => $groupData['description'],
            'enabled'     => $groupData['enabled'],
            'created_by'  => $groupData['created_by'],
            'updated_by'  => $groupData['updated_by'],
            'create_time' => time(),
            'update_time' => time(),
            'delete_time' => null,
        ];

        $db->table($tableName)->insert($data);
        return $id;
    }

    /**
     * 添加字典项
     *
     * @param array      $itemData
     * @param            $db
     * @param int|string $dictId
     * @param string     $tableName
     *
     * @return int|string
     */
    protected function addDictItem(array $itemData, $db, int|string $dictId, string $tableName = "ma_system_dict_item"): int|string
    {
        $id   = $this->generateSnowflakeID(); // 生成 ID
        $data = [
            'id'          => $id,
            'dict_id'     => $dictId, // 关联字典组 ID
            'label'       => $itemData['label'],
            'value'       => $itemData['value'],
            'code'        => $itemData['code'],
            'sort'        => $itemData['sort'],
            'enabled'     => $itemData['enabled'],
            'created_by'  => $itemData['created_by'],
            'updated_by'  => $itemData['updated_by'],
            'create_time' => time(),
            'update_time' => time(),
            'remark'      => $itemData['remark'] ?? null,
            'delete_time' => null,
        ];
        $db->table($tableName)->insert($data);
        return $id; // 返回生成的 ID
    }

    /**
     * 导入字典
     *
     * @param array $dictData
     * @param       $db
     */
    protected function importDict(array $dictData, $db): void
    {
        foreach ($dictData as $groupData) {
            // 添加字典组并获取组 ID
            $dictId = $this->addDictGroup($groupData, $db);
            usleep(10);
            // 处理字典项
            if (!empty($groupData['items'])) {
                foreach ($groupData['items'] as $itemData) {
                    $this->addDictItem($itemData, $db, $dictId);
                    usleep(10);
                }
            }
        }
    }

    /**
     * 导入系统配置
     *
     * @param array  $configData
     * @param        $db
     * @param string $tableName
     */
    protected function importConfiguration(array $configData, $db, string $tableName = "ma_system_config")
    {
        foreach ($configData as $item) {
            $id   = $this->generateSnowflakeID(); // 生成 ID
            $data = [
                'id'          => $id,
                'group_code'  => $item['group_code'],
                'code'        => $item['code'],
                'name'        => $item['name'],
                'content'     => json_encode($item['content'] ?? (object)[]),
                'is_sys'      => $item['is_sys'],
                'enabled'     => $item['enabled'],
                'create_time' => time(),
                'create_user' => $item['create_user'],
                'update_time' => time(),
                'update_user' => $item['update_user'],
                'delete_time' => null,
                'remark'      => $item['remark'] ?? null,
            ];
            $db->table($tableName)->insert($data);
            usleep(10);
        }
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
    }

}
