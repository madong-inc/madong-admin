<?php

namespace core\plugin\traits;

use core\uuid\Snowflake;
use support\Db;

/**
 * 菜单操作 Trait
 */
trait MenuTrait
{
    /**
     * 加载菜单文件
     */
    protected function loadMenus(): void
    {
        $menuResourceDir = $this->getConfig('resource.menu', 'menu');
        $menuDir = $this->pluginPath . '/resource/' . $menuResourceDir;

        $this->output("📂 Loading menus from: {$menuDir}");

        if (!is_dir($menuDir)) {
            $this->output("📂 No menu directory found, skipping...");
            return;
        }

        $files = scandir($menuDir);
        $this->output("📂 Menu files found: " . implode(', ', array_diff($files, ['.', '..'])));

        $this->loadMenuFile($menuDir . '/admin.php', 'admin');
        $this->loadMenuFile($menuDir . '/frontend.php', 'frontend');
        $this->loadMenuFile($menuDir . '/web.php', 'web');
    }

    /**
     * 加载单个菜单文件
     */
    protected function loadMenuFile(string $filePath, string $type): void
    {
        if (!file_exists($filePath)) {
            $this->output("  ⚠️ {$type}: Menu file not found");
            return;
        }

        $menu = include $filePath;

        if (empty($menu)) {
            $this->output("  ⚠️ {$type}: Empty menu file");
            return;
        }

        if (!is_array($menu)) {
            $this->output("  ❌ {$type}: Invalid menu format");
            return;
        }

        $this->saveMenuToSystem($type, $menu);

        $this->output("  ✅ {$type}: Menu loaded (" . count($menu) . " items)");
    }

    /**
     * 保存菜单到系统
     */
    protected function saveMenuToSystem(string $type, array $menu): void
    {
        if ($type === 'admin') {
            $this->saveAdminMenu($menu);
        } else {
            $this->saveWebMenu($menu);
        }
    }

    /**
     * 保存后台菜单
     */
    protected function saveAdminMenu(array $menu): void
    {
        $tableName = 'sys_menu';

        $this->output("  📝 Saving admin menu to {$tableName}...");

        try {
            $connection = $this->connection ?? null;
            $hasTable = Db::connection($connection)->getSchemaBuilder()->hasTable($tableName);

            if (!$hasTable) {
                $this->output("  ⚠️ Table {$tableName} does not exist, skipping menu import");
                return;
            }

            $this->insertAdminMenuItems($tableName, $menu, $this->pluginName);

            $this->output("  ✅ Admin menu saved to {$tableName}");
        } catch (\Throwable $e) {
            $this->output("  ❌ Failed to save admin menu: {$e->getMessage()}");
        }
    }

    /**
     * 插入后台菜单项
     */
    protected function insertAdminMenuItems(string $tableName, array $items, string $plugin, int $parentId = 0): void
    {
        $connection = $this->connection ?? null;

        foreach ($items as $item) {
            $menuId = (int)Snowflake::generate();

            $data = [
                'id'         => $menuId,
                'pid'        => $parentId,
                'app'        => 'admin',
                'title'      => $item['name'] ?? '',
                'code'       => $item['code'] ?? '',
                'level'      => $item['level'] ?? 1,
                'type'       => $item['type'] ?? 1,
                'sort'       => $item['sort'] ?? 0,
                'path'       => $item['path'] ?? '',
                'component'  => $item['component'] ?? '',
                'icon'       => $item['icon'] ?? '',
                'is_show'   => $item['is_show'] ?? 1,
                'is_link'   => $item['is_link'] ?? 0,
                'is_cache'  => $item['is_cache'] ?? 0,
                'is_sync'   => $item['is_sync'] ?? 0,
                'source'     => 'plugin',
                'platform'   => $plugin,
                'created_at' => time(),
                'updated_at' => time(),
            ];

            Db::connection($connection)->table($tableName)->insert($data);

            if (!empty($item['children'])) {
                $this->insertAdminMenuItems($tableName, $item['children'], $plugin, $menuId);
            }
        }
    }

    /**
     * 保存前台菜单
     */
    protected function saveWebMenu(array $menu): void
    {
        $tableName = 'web_menu';

        $this->output("  📝 Saving web menu to {$tableName}...");

        try {
            $connection = $this->connection ?? null;
            $hasTable = Db::connection($connection)->getSchemaBuilder()->hasTable($tableName);

            if (!$hasTable) {
                $this->output("  ⚠️ Table {$tableName} does not exist, skipping menu import");
                return;
            }

            $this->insertWebMenuItems($tableName, $menu, $this->pluginName);

            $this->output("  ✅ Web menu saved to {$tableName}");
        } catch (\Throwable $e) {
            $this->output("  ❌ Failed to save web menu: {$e->getMessage()}");
        }
    }

    /**
     * 插入前台菜单项
     */
    protected function insertWebMenuItems(string $tableName, array $items, string $plugin, int $parentId = 0): void
    {
        $connection = $this->connection ?? null;

        foreach ($items as $item) {
            $menuId = (int)Snowflake::generate();

            $data = [
                'id'         => $menuId,
                'app'        => 'web',
                'category'   => $item['category'] ?? 1,
                'source'     => 'plugin',
                'code'       => $item['code'] ?? '',
                'name'       => $item['name'] ?? '',
                'url'        => $item['path'] ?? $item['url'] ?? '',
                'pid'        => $parentId,
                'level'      => $item['level'] ?? 1,
                'type'       => $item['type'] ?? 1,
                'sort'       => $item['sort'] ?? 0,
                'target'     => $item['target'] ?? '_self',
                'icon'       => $item['icon'] ?? '',
                'is_show'   => $item['is_show'] ?? 1,
                'enabled'   => $item['enabled'] ?? 1,
                'created_at' => time(),
                'updated_at' => time(),
            ];

            Db::connection($connection)->table($tableName)->insert($data);

            if (!empty($item['children'])) {
                $this->insertWebMenuItems($tableName, $item['children'], $plugin, $menuId);
            }
        }
    }

    /**
     * 清除插件菜单
     */
    protected function clearPluginMenus(string $plugin): void
    {
        $this->clearMenusByTable('sys_menu', $plugin);
        $this->clearMenusByTable('web_menu', $plugin);
    }

    /**
     * 根据表清除菜单
     */
    protected function clearMenusByTable(string $tableName, string $plugin): void
    {
        try {
            $connection = $this->connection ?? null;
            if (!Db::connection($connection)->getSchemaBuilder()->hasTable($tableName)) {
                return;
            }

            $column = $tableName === 'sys_menu' ? 'platform' : 'source';
            $value = $tableName === 'sys_menu' ? $plugin : 'plugin';

            $deleted = Db::connection($connection)->table($tableName)->where($column, $value)->delete();
            if ($deleted > 0) {
                $this->output("  🗑️ Cleared {$deleted} menus from {$tableName}");
            }
        } catch (\Throwable $e) {
            $this->output("  ⚠️ Failed to clear {$tableName}: {$e->getMessage()}");
        }
    }

    /**
     * 导入插件菜单（快捷方法）
     */
    public function importPluginMenus(): void
    {
        $this->importMenus();
    }

    /**
     * 卸载插件菜单（快捷方法）
     */
    public function uninstallPluginMenus(): void
    {
        $this->clearMenus();
    }

    /**
     * 导入菜单（子类可重写）
     */
    protected function importMenus(): void
    {
        $this->loadMenus();
    }

    /**
     * 清除菜单（子类可重写）
     */
    protected function clearMenus(): void
    {
        $this->clearPluginMenus($this->pluginName);
    }
}
