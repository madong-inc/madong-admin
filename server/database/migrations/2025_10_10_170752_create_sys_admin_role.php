<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_admin_role', function (Blueprint $table) {
            $table->comment('用户与角色关联表');
            $table->bigInteger('admin_id')->comment('管理员主键');
            $table->bigInteger('role_id')->comment('角色主键');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_admin_role');
    }
};
