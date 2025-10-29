<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_crontab_log', function (Blueprint $table) {
            $table->comment('定时器任务执行日志表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键ID');
            $table->bigInteger('crontab_id')->unsigned()->comment('任务id');
            $table->string('target', 255)->nullable()->default(null)->comment('任务调用目标字符串');
            $table->longText('log')->nullable()->comment('任务执行日志');
            $table->tinyInteger('return_code')->default(1)->comment('执行返回状态,1成功,0失败');
            $table->string('running_time', 10)->comment('执行所用时间');
            $table->bigInteger('created_at')->default(0)->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_crontab_log');
    }
};
