<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_admin_casbin', function (Blueprint $table) {
            $table->bigInteger('admin_id', 20)->comment('管理员主键（关联 admins 表的 id 字段）');
            $table->string('admin_casbin_id', 120)->comment('对应 casbin 策略表的键（如 p_policy 或 g_policy 的 key）');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_admin_casbin');
    }
};
