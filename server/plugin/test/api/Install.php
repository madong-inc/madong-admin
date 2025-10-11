<?php

namespace plugin\test\api;

use plugin\admin\api\Menu;
use support\Db;
use Throwable;

class Install
{

    /**
     * 数据库连接
     */
    protected static $connection = 'mysql';
    
    /**
     * 安装
     *
     * @param $version
     * @return void
     */
    public static function install($version)
    {
        // 安装数据库
        static::installSql();
        // 导入菜单
        if($menus = static::getMenus()) {
            Menu::import($menus);
        }
    }

    /**
     * 卸载
     *
     * @param $version
     * @return void
     */
    public static function uninstall($version)
    {
        // 删除菜单
        foreach (static::getMenus() as $menu) {
            Menu::delete($menu['key']);
        }
        // 卸载数据库
        static::uninstallSql();
    }

    /**
     * 更新
     *
     * @param $from_version
     * @param $to_version
     * @param $context
     * @return void
     */
    public static function update($from_version, $to_version, $context = null)
    {
        // 删除不用的菜单
        if (isset($context['previous_menus'])) {
            static::removeUnnecessaryMenus($context['previous_menus']);
        }
        // 安装数据库
        static::installSql();
        // 导入新菜单
        if ($menus = static::getMenus()) {
            Menu::import($menus);
        }
        // 执行更新操作
        $update_file = __DIR__ . '/../update.php';
        if (is_file($update_file)) {
            include $update_file;
        }
    }

    /**
     * 更新前数据收集等
     *
     * @param $from_version
     * @param $to_version
     * @return array|array[]
     */
    public static function beforeUpdate($from_version, $to_version)
    {
        // 在更新之前获得老菜单，通过context传递给 update
        return ['previous_menus' => static::getMenus()];
    }

    /**
     * 获取菜单
     *
     * @return array|mixed
     */
    public static function getMenus()
    {
        clearstatcache();
        if (is_file($menu_file = __DIR__ . '/../config/menu.php')) {
            $menus = include $menu_file;
            return $menus ?: [];
        }
        return [];
    }

    /**
     * 删除不需要的菜单
     *
     * @param $previous_menus
     * @return void
     */
    public static function removeUnnecessaryMenus($previous_menus)
    {
        $menus_to_remove = array_diff(Menu::column($previous_menus, 'name'), Menu::column(static::getMenus(), 'name'));
        foreach ($menus_to_remove as $name) {
            Menu::delete($name);
        }
    }
    
    /**
     * 安装SQL
     *
     * @return void
     */
    protected static function installSql()
    {
        static::importSql(__DIR__ . '/../install.sql');
    }
    
    /**
     * 卸载SQL
     *
     * @return void
     */
    protected static function uninstallSql() {
        // 如果卸载数据库文件存在责直接使用
        $uninstallSqlFile = __DIR__ . '/../uninstall.sql';
        if (is_file($uninstallSqlFile)) {
            static::importSql($uninstallSqlFile);
            return;
        }
        // 否则根据install.sql生成卸载数据库文件uninstall.sql
        $installSqlFile = __DIR__ . '/../install.sql';
        if (!is_file($installSqlFile)) {
            return;
        }
        $installSql = file_get_contents($installSqlFile);
        preg_match_all('/CREATE TABLE `(.+?)`/si', $installSql, $matches);
        $dropSql = '';
        foreach ($matches[1] as $table) {
            $dropSql .= "DROP TABLE IF EXISTS `$table`;\n";
        }
        file_put_contents($uninstallSqlFile, $dropSql);
        static::importSql($uninstallSqlFile);
        unlink($uninstallSqlFile);
    }
    
    /**
     * 导入数据库
     *
     * @return void
     */
    public static function importSql($mysqlDumpFile)
    {
        if (!$mysqlDumpFile || !is_file($mysqlDumpFile)) {
            return;
        }
        foreach (explode(';', file_get_contents($mysqlDumpFile)) as $sql) {
            if ($sql = trim($sql)) {
                try {
                    Db::connection(static::$connection)->statement($sql);
                } catch (Throwable $e) {}
            }
        }
    }

}