<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_dict', function (Blueprint $table) {
            $table->comment('字典类型表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->string('group_code', 50)->nullable()->default(null)->comment('字典类型');
            $table->string('name', 50)->nullable()->default(null)->comment('字典名称');
            $table->string('code', 100)->nullable()->default(null)->comment('字典标示');
            $table->bigInteger('sort')->nullable()->default(0)->comment('排序');
            $table->smallInteger('data_type')->nullable()->default(1)->comment('数据类型');
            $table->longText('description')->nullable()->comment('描述');
            $table->smallInteger('enabled')->nullable()->default(1)->comment('状态 (1正常 0停用)');
            $table->bigInteger('created_by')->nullable()->comment('创建者');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_dict');
    }
};
