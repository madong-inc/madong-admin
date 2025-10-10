<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_crontab', function (Blueprint $table) {
            // 主键：id（bigint unsigned，非空）
            $table->bigInteger('id')->unsigned()->primary()->comment('主键ID');

            // 业务ID（可空）
            $table->string('biz_id', 36)->nullable()->default(null)->comment('业务id');

            // 任务标题（非空）
            $table->string('title', 255)->comment('任务标题');

            // 任务类型（tinyint，非空，默认1：url；2：eval；3：shell）
            $table->tinyInteger('type')->default(1)->comment('任务类型1 url,2 eval,3 shell');

            // 任务周期（tinyint，非空，默认1）
            $table->tinyInteger('task_cycle')->default(1)->comment('任务周期');

            // 周期规则（JSON格式，可空）
            $table->json('cycle_rule')->nullable()->comment('任务周期规则(JSON格式)');

            // 任务表达式（长文本，可空）
            $table->longText('rule')->nullable()->comment('任务表达式');

            // 调用任务字符串（长文本，可空）
            $table->longText('target')->nullable()->comment('调用任务字符串');

            // 已运行次数（int，非空，默认0）
            $table->integer('running_times')->default(0)->comment('已运行次数');

            // 上次运行时间（int，非空，默认0）
            $table->integer('last_running_time')->default(0)->comment('上次运行时间');

            // 任务状态（tinyint，非空，默认0：禁用；1：启用）
            $table->tinyInteger('enabled')->default(0)->comment('任务状态0禁用,1启用');

            // 创建时间（int，非空，默认0）
            $table->integer('created_at')->default(0)->comment('创建时间');

            // 创建人（bigint，可空）
            $table->bigInteger('created_by')->nullable()->comment('创建人');

            // 软删除时间（bigint，非空，默认0）
            $table->bigInteger('deleted_at')->default(0)->comment('软删除时间');

            // 更新时间（bigint，可空）
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');

            // 更新人（bigint，可空）
            $table->bigInteger('updated_by')->nullable()->comment('更新人');

            // 是否循环执行（tinyint，可空，默认1）
            $table->tinyInteger('singleton')->nullable()->default(1)->comment('是否循环执行1 ');

            // --------------------------
            // 2. 索引配置（匹配原SQL）
            // --------------------------
            // 标题索引（原SQL：INDEX title(title)）
            $table->index('title');

            // 状态索引（原SQL：INDEX status(enabled)，索引名`status`对应字段`enabled`）
            $table->index('enabled', 'status');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_crontab');
    }
};
