<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_login_log', function (Blueprint $table) {
            $table->comment('登录日志表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->string('user_name', 20)->nullable()->default(null)->comment('用户名');
            $table->string('ip', 45)->nullable()->default(null)->comment('登录IP地址');
            $table->string('ip_location', 255)->nullable()->default(null)->comment('IP所属地');
            $table->string('os', 50)->nullable()->default(null)->comment('操作系统');
            $table->string('browser', 50)->nullable()->default(null)->comment('浏览器');
            $table->smallInteger('status')->nullable()->default(1)->comment('登录状态 (1成功 2失败)');
            $table->longText('message')->nullable()->comment('提示消息');
            $table->bigInteger('login_time')->nullable()->comment('登录时间');
            $table->longText('key')->nullable()->comment('key');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('expires_at')->nullable()->comment('过期时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            $table->string('remark', 255)->nullable()->default(null)->comment('备注');

            $table->index('user_name', 'username');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_login_log');
    }
};
