<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_post', function (Blueprint $table) {
            $table->comment('岗位信息表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->bigInteger('dept_id')->nullable()->comment('部门id');
            $table->string('code', 100)->comment('岗位代码');
            $table->string('name', 50)->comment('岗位名称');
            $table->smallInteger('sort')->unsigned()->nullable()->default(0)->comment('排序');
            $table->smallInteger('enabled')->nullable()->default(1)->comment('状态 (1正常 0停用)');
            $table->bigInteger('created_by')->nullable()->comment('创建者');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            $table->string('remark', 255)->nullable()->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_post）
        $schema->dropIfExists('sys_post');
    }
};
