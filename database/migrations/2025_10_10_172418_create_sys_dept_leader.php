<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_dept_leader', function (Blueprint $table) {
            $table->comment('部门领导关联表');
            $table->bigInteger('dept_id')->comment('部门主键');
            $table->bigInteger('admin_id')->comment('管理员主键');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_dept_leader');
    }
};
