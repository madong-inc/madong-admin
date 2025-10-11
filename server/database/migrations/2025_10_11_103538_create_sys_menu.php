<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_menu', function (Blueprint $table) {
            $table->comment('菜单信息表');
            $table->bigInteger('id')->unsigned()->primary()->comment('菜单ID');
            $table->bigInteger('pid')->default(0)->comment('父ID');
            $table->string('app', 32)->default('admin')->comment('应用编码');
            $table->string('title', 64)->comment('菜单名称');
            $table->string('code', 64)->nullable()->comment('唯一编码');
            $table->string('level', 255)->nullable()->comment('父ID集合');
            $table->integer('type')->nullable()->comment('菜单类型1=>目录  2>菜单 3=>按钮 4=>接口 5=>内链 6=>外链');
            $table->bigInteger('sort')->nullable()->default(999)->comment('排序');
            $table->string('path', 100)->nullable()->comment('路由地址');
            $table->string('component', 100)->nullable()->comment('组件地址');
            $table->string('redirect', 255)->nullable()->comment('重定向');
            $table->string('icon', 64)->nullable()->comment('菜单图标');
            $table->tinyInteger('is_show')->nullable()->default(1)->comment('是否显示 0=>否   1=>是');
            $table->tinyInteger('is_link')->nullable()->default(0)->comment('是否外链 0=>否   1=>是');
            $table->longText('link_url')->nullable()->comment('外部链接地址');
            $table->tinyInteger('enabled')->nullable()->default(1)->comment('状态 (1正常 0停用)');
            $table->integer('open_type')->nullable()->default(0)->comment('是否外链 1=>是    0=>否');
            $table->tinyInteger('is_cache')->nullable()->default(0)->comment('是否缓存 1=>是    0=>否');
            $table->tinyInteger('is_sync')->nullable()->default(1)->comment('是否同步');
            $table->tinyInteger('is_affix')->nullable()->default(0)->comment('是否固定tags无法关闭');
            $table->tinyInteger('is_global')->nullable()->default(0)->comment('是否全局公共菜单 ');
            $table->string('variable', 500)->nullable()->comment('额外参数JSON');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('created_by')->nullable()->comment('创建用户');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->bigInteger('updated_by')->nullable()->comment('更新用户');
            $table->bigInteger('deleted_at')->nullable()->default(null)->comment('是否删除');
            $table->string('methods', 10)->default('get')->comment('请求方法');
            $table->tinyInteger('is_frame')->nullable()->comment('是否外链');

            $table->index('code', 'idx_sys_menu_code');
            $table->index('app', 'idx_sys_menu_app_code');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_menu）
        $schema->dropIfExists('sys_menu');
    }
};
