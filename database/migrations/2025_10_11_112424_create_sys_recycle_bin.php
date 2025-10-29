<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_recycle_bin', function (Blueprint $table) {
            $table->comment('数据回收记录表');
            $table->bigInteger('id')->unsigned()->primary()->comment('ID');
            $table->bigInteger('original_id')->nullable()->comment('原始数据ID');
            $table->json('data')->nullable()->comment('回收的数据');
            $table->string('table_name', 100)->default('')->comment('数据表');
            $table->string('table_prefix', 50)->nullable()->comment('表前缀');
            $table->tinyInteger('enabled')->unsigned()->default(0)->comment('是否已还原');
            $table->string('ip', 50)->default('')->comment('操作者IP');
            $table->bigInteger('operate_by')->unsigned()->default(0)->comment('操作管理员');
            $table->bigInteger('created_at')->unsigned()->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_recycle_bin');
    }
};
