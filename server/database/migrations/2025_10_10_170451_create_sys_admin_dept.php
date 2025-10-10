<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_admin_dept', function (Blueprint $table) {
            $table->bigInteger('admin_id')->comment('用户主键');
            $table->bigInteger('dept_id')->comment('部门主键');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_admin_dept');
    }
};
