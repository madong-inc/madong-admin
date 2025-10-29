<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_role_casbin', function (Blueprint $table) {
            $table->comment('角色与策略关联表');
            $table->bigInteger('role_id')->unsigned()->comment('管理员主键');
            $table->string('role_casbin_id', 50)->comment('对应casbin策略表');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_role_casbin');
    }
};
