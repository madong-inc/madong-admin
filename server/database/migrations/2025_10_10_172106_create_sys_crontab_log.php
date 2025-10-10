<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_crontab_log', function (Blueprint $table) {
            // 1. 主键：执行日志ID
            $table->bigInteger('id')
                ->unsigned()
                ->primary()
                ->comment('主键ID');

            // 2. 关联任务ID（非空，对应sys_crontab表的id）
            $table->bigInteger('crontab_id')
                ->unsigned()
                ->comment('任务id');

            // 3. 任务调用目标字符串（可空）
            $table->string('target', 255)
                ->nullable()
                ->default(null)
                ->comment('任务调用目标字符串');

            // 4. 执行日志（长文本，可空）
            $table->longText('log')
                ->nullable()
                ->comment('任务执行日志');

            // 5. 执行返回状态（非空，默认1：成功；0：失败）
            $table->tinyInteger('return_code')
                ->default(1)
                ->comment('执行返回状态,1成功,0失败');

            // 6. 执行所用时间（非空，varchar(10)）
            $table->string('running_time', 10)
                ->comment('执行所用时间');

            // 7. 创建时间（非空，默认0）
            $table->bigInteger('created_at')
                ->default(0)
                ->comment('创建时间');

            // 8. 更新时间（可空）
            $table->bigInteger('updated_at')
                ->nullable()
                ->comment('更新时间');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_crontab_log');
    }
};
