<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_admin_post', function (Blueprint $table) {
            $table->bigInteger('admin_id')->comment('管理员主键');
            $table->bigInteger('post_id')->comment('岗位主键');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_admin_post');
    }
};
