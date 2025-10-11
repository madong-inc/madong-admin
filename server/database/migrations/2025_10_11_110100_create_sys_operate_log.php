<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_operate_log', function (Blueprint $table) {
            $table->comment('操作日志表');
            $table->bigInteger('id')->primary()->comment('主键');
            $table->string('name', 50)->nullable()->default(null)->comment('内容');
            $table->string('app', 50)->nullable()->default(null)->comment('应用名称');
            $table->string('ip', 255)->nullable()->default(null)->comment('请求ip');
            $table->string('ip_location', 255)->nullable()->default(null)->comment('请求ip归属地');
            $table->string('browser', 255)->nullable()->default(null)->comment('浏览器');
            $table->string('os', 255)->nullable()->default(null)->comment('操作系统');
            $table->string('url', 500)->nullable()->default(null)->comment('请求地址');
            $table->string('class_name', 500)->nullable()->default(null)->comment('类名称');
            $table->string('action', 500)->nullable()->default(null)->comment('方法名称');
            $table->string('method', 255)->nullable()->default(null)->comment('请求方式（GET POST PUT DELETE）');
            $table->longText('param')->nullable()->comment('请求参数');
            $table->longText('result')->nullable()->comment('返回结果');
            $table->bigInteger('created_at')->nullable()->comment('操作时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->string('user_name', 50)->nullable()->default(null)->comment('操作账号');

        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_operate_log');
    }
};
