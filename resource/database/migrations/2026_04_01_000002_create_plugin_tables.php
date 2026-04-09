<?php

/**
 * 创建插件相关表
 * 
 * 表:
 * - plugin (插件表)
 * - plugin_log (插件日志表)
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return new class {

    public function up(Builder $schema): void
    {
        // 1. 插件表
        if (!$schema->hasTable('plugin')) {
            $schema->create('plugin', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('title', 128)->comment('插件标题');
                $table->longText('icon')->nullable()->comment('插件图标');
                $table->string('key', 64)->unique()->comment('插件标识');
                $table->string('desc', 255)->nullable()->comment('插件描述');
                $table->tinyInteger('status')->default(1)->comment('状态: 1启用 0禁用');
                $table->string('author', 64)->nullable()->comment('作者');
                $table->string('version', 20)->comment('插件版本');
                $table->string('type', 32)->default('custom')->comment('插件类型');
                $table->longText('cover')->nullable()->comment('插件封面');
                $table->longText('variables')->nullable()->comment('插件变量配置');
                $table->string('support_app', 32)->default('admin')->comment('支持的终端');
                $table->bigInteger('installed_at')->nullable()->comment('安装时间');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->index('key');
            });
        }

        // 2. 插件日志表
        if (!$schema->hasTable('plugin_log')) {
            $schema->create('plugin_log', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('action', 32)->comment('操作类型');
                $table->string('key', 64)->comment('插件标识');
                $table->string('pre_upgrade_version', 20)->nullable()->comment('升级前版本');
                $table->string('post_upgrade_version', 20)->nullable()->comment('升级后版本');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->index('key');
                $table->index('action');
            });
        }

        echo "Created plugin tables.\n";
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('plugin_log');
        $schema->dropIfExists('plugin');
    }
};