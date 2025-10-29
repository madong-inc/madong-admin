<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_crontab', function (Blueprint $table) {
            $table->comment('定时器任务表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键ID');
            $table->string('biz_id', 36)->nullable()->default(null)->comment('业务id');
            $table->string('title', 255)->comment('任务标题');
            $table->tinyInteger('type')->default(1)->comment('任务类型1 url,2 eval,3 shell');
            $table->tinyInteger('task_cycle')->default(1)->comment('任务周期');
            $table->json('cycle_rule')->nullable()->comment('任务周期规则(JSON格式)');
            $table->longText('rule')->nullable()->comment('任务表达式');
            $table->longText('target')->nullable()->comment('调用任务字符串');
            $table->integer('running_times')->default(0)->comment('已运行次数');
            $table->integer('last_running_time')->default(0)->comment('上次运行时间');
            $table->tinyInteger('enabled')->default(0)->comment('任务状态0禁用,1启用');
            $table->integer('created_at')->default(0)->comment('创建时间');
            $table->bigInteger('created_by')->nullable()->comment('创建人');
            $table->bigInteger('deleted_at')->default(0)->comment('软删除时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->bigInteger('updated_by')->nullable()->comment('更新人');
            $table->tinyInteger('singleton')->nullable()->default(1)->comment('是否循环执行1 ');

            $table->index('title');
            $table->index('enabled', 'status');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_crontab');
    }
};
