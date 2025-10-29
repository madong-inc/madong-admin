<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_route', function (Blueprint $table) {
            $table->comment('路由规则表');
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('cate_id')->default(0)->comment('分组id');
            $table->string('app_name', 20)->nullable()->default('api')->comment('应用名');
            $table->string('name', 50)->default('')->comment('路由名称');
            $table->text('describe')->nullable()->comment('功能描述');
            $table->string('path', 100)->nullable()->default('')->comment('路由路径');
            $table->enum('method', ['POST', 'GET', 'DELETE', 'PUT', '*'])->nullable()->default('GET')->comment('路由请求方式');
            $table->string('file_path', 255)->nullable()->default('')->comment('文件路径');
            $table->string('action', 255)->nullable()->default('')->comment('方法名称');
            $table->longText('query')->nullable()->comment('get请求参数');
            $table->longText('header')->nullable()->comment('header');
            $table->longText('request')->nullable()->comment('请求数据');
            $table->string('request_type', 100)->nullable()->default('')->comment('请求类型');
            $table->longText('response')->nullable()->comment('返回数据');
            $table->longText('request_example')->nullable()->comment('请求示例');
            $table->longText('response_example')->nullable()->comment('返回示例');
            $table->bigInteger('created_at')->nullable()->comment('添加时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');

            $table->index('path', 'path');

        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_route）
        $schema->dropIfExists('sys_route');
    }
};
