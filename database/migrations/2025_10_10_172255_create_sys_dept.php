<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_dept', function (Blueprint $table) {
            $table->comment('部门信息表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->bigInteger('pid')->unsigned()->nullable()->default(0)->comment('父ID');
            $table->string('level', 500)->nullable()->default(null)->comment('组级集合');
            $table->string('code', 50)->nullable()->default(null)->comment('部门唯一编码');
            $table->string('name', 30)->nullable()->default(null)->comment('部门名称');
            $table->string('main_leader_id', 20)->nullable()->default(null)->comment('负责人');
            $table->string('phone', 11)->nullable()->default(null)->comment('联系电话');
            $table->smallInteger('enabled')->nullable()->default(1)->comment('状态 (1正常 0停用)');
            $table->smallInteger('sort')->unsigned()->nullable()->default(0)->comment('排序');
            $table->bigInteger('created_by')->nullable()->comment('创建者');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            $table->longText('remark')->nullable()->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_dept');
    }
};
