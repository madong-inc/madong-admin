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
                $table->string('name', 64)->comment('插件名称');
                $table->string('code', 64)->unique()->comment('插件编码');
                $table->string('version', 20)->comment('插件版本');
                $table->string('author', 64)->nullable()->comment('作者');
                $table->string('description', 255)->nullable()->comment('插件描述');
                $table->string('homepage', 255)->nullable()->comment('插件官网');
                $table->string('icon', 255)->nullable()->comment('插件图标');
                $table->tinyInteger('enabled')->default(0)->comment('状态: 1启用 0禁用');
                $table->tinyInteger('installed')->default(1)->comment('是否安装: 1是 0否');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->index('code');
            });
        }

        // 2. 插件日志表
        if (!$schema->hasTable('plugin_log')) {
            $schema->create('plugin_log', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('plugin_id')->comment('插件ID');
                $table->string('action', 32)->comment('操作类型');
                $table->string('version', 20)->comment('操作版本');
                $table->longText('content')->nullable()->comment('操作内容');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->index('plugin_id');
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