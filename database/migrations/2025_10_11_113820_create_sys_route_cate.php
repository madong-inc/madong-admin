<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_route_cate', function (Blueprint $table) {
            $table->comment('路由规则分组表');
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('pid')->default(0)->comment('上级id');
            $table->string('app_name', 20)->nullable()->default('')->comment('应用名');
            $table->string('name', 50)->default('')->comment('名称');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->tinyInteger('enabled')->nullable()->default(1)->comment('状态');
            $table->bigInteger('created_at')->nullable()->default(0)->comment('添加时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');

            // 索引（匹配原SQL）
            $table->index('app_name', 'app_name');

        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_route_cate）
        $schema->dropIfExists('sys_route_cate');
    }
};
