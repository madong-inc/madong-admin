<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_config', function (Blueprint $table) {
            $table->comment('配置表');
            $table->bigInteger('id')->unsigned()->primary()->comment('配置ID');
            $table->string('group_code', 64)->nullable()->default(null)->comment('分组编码');
            $table->string('code', 64)->comment('唯一编码');
            $table->string('name', 64)->comment('配置名称');
            $table->longText('content')->nullable()->comment('配置内容');
            $table->tinyInteger('is_sys')->nullable()->default(0)->comment('是否系统');
            $table->tinyInteger('enabled')->nullable()->default(1)->comment('是否启用');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('created_by')->nullable()->comment('创建用户');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->bigInteger('updated_by')->nullable()->comment('更新用户');
            $table->bigInteger('deleted_at')->nullable()->comment('是否删除');
            $table->longText('remark')->nullable()->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_config）
        $schema->dropIfExists('sys_config');
    }
};
