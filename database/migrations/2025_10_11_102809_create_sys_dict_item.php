<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {

        $schema->create('sys_dict_item', function (Blueprint $table) {
            $table->comment('字典数据表');
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('dict_id')->unsigned()->nullable()->default(null)->comment('字典类型ID');
            $table->string('label', 50)->nullable()->default(null)->comment('字典标签');
            $table->string('value', 100)->nullable()->default(null)->comment('字典值');
            $table->string('code', 100)->nullable()->default(null)->comment('字典标示');
            $table->string('color', 50)->nullable()->default(null)->comment('tag颜色');
            $table->string('other_class', 50)->nullable()->default(null)->comment('other_class');
            $table->smallInteger('sort')->unsigned()->nullable()->default(0)->comment('排序');
            $table->smallInteger('enabled')->nullable()->default(1)->comment('状态 (1正常 0停用)');
            $table->bigInteger('created_by')->nullable()->comment('创建者');
            $table->bigInteger('updated_by')->nullable()->comment('更新者');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            $table->longText('remark')->nullable()->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_dict_item');
    }
};
