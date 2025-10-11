<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_notice', function (Blueprint $table) {
            $table->comment('通知公告表');
            $table->bigInteger('id')->primary()->comment('公告ID');
            $table->enum('type', ['announcement', 'notice'])->default('announcement')->comment('公告类型（notice=>通知 announcement=>公告）');
            $table->string('title', 50)->comment('公告标题');
            $table->longText('content')->nullable()->comment('公告内容');
            $table->integer('sort')->nullable()->default(10)->comment('排序');
            $table->tinyInteger('enabled')->nullable()->default(0)->comment('公告状态（0正常 1关闭）');
            $table->string('uuid', 50)->nullable()->comment('uuid');
            $table->bigInteger('created_dept')->nullable()->comment('创建部门');
            $table->bigInteger('created_by')->nullable()->comment('创建者');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->string('remark', 255)->nullable()->comment('备注');

        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_notice');
    }
};
