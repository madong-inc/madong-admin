<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_role', function (Blueprint $table) {
            $table->comment('角色信息表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->bigInteger('pid')->nullable()->default(0)->comment('父级id');
            $table->string('name', 30)->nullable()->comment('角色名称');
            $table->string('code', 100)->nullable()->comment('角色代码');
            $table->tinyInteger('is_super_admin')->nullable()->default(0)->comment('是否超级管理员 1=是   0=否');
            $table->tinyInteger('role_type')->nullable()->comment('角色类型');
            $table->smallInteger('data_scope')->nullable()->default(1)->comment('数据范围(1:全部数据权限 2:自定义数据权限 3:本部门数据权限 4:本部门及以下数据权限 5:本人数据权限)');
            $table->smallInteger('enabled')->nullable()->default(1)->comment('状态 (1正常 0停用)');
            $table->smallInteger('sort')->unsigned()->nullable()->default(0)->comment('排序');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->bigInteger('created_by')->nullable()->comment('创建者');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_role）
        $schema->dropIfExists('sys_role');
    }
};
