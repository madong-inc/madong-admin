<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_role_dept', function (Blueprint $table) {
            $table->comment('角色与部门关联表');
            $table->bigInteger('role_id')->unsigned()->comment('角色id');
            $table->bigInteger('dept_id')->unsigned()->comment('部门id');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_role_dept');
    }
};
