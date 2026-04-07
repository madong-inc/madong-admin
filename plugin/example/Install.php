<?php

namespace plugin\example;

use core\plugin\PluginInstall as BasePluginInstall;

/**
 * 插件安装类
 * 继承 PluginInstall 基类，自动处理：
 * - 数据库迁移 (resource/database/migrations/)
 * - 种子数据 (resource/database/seeds/)
 * 
 * 菜单和模板需要按需导入，在 afterInstall/afterUninstall 中调用：
 * - $this->importPluginMenus()      - 导入菜单
 * - $this->importTemplates()        - 复制模板
 * - $this->uninstallPluginMenus()   - 卸载菜单
 * - $this->deletePluginTemplates()  - 删除模板
 */
class Install extends BasePluginInstall
{


    /**
     * 安装前回调
     */
    protected function beforeInstall(string $version): void
    {
        echo "  🔄 Running beforeInstall...\n";
    }


    /**
     * 安装后回调 - 按需导入菜单和模板
     */
    protected function afterInstall(string $version): void
    {
        echo "  🔄 Running afterInstall...\n";
        
        // 导入菜单
        $this->importPluginMenus();
        
        // 复制模板资源
        $this->importTemplates();
    }
    
    /**
     * 卸载前回调
     */
    protected function beforeUninstall(string $version): void
    {
        echo "  🔄 Running beforeUninstall...\n";
    }

    /**
     * 卸载后回调 - 清理菜单和模板
     */
    protected function afterUninstall(string $version): void
    {
        echo "  🔄 Running afterUninstall...\n";
        // 卸载菜单
        $this->uninstallPluginMenus();
        // 删除模板资源
        $this->deletePluginTemplates();
    }
}
