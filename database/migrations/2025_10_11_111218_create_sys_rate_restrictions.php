<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_rate_restrictions', function (Blueprint $table) {
            $table->comment('限制访问名单表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->string('ip', 50)->nullable()->comment('IP地址');
            $table->string('name', 64)->comment('名称');
            $table->tinyInteger('enabled')->nullable()->default(1)->comment('规则状态(0-禁用,1-启用)');
            $table->integer('priority')->nullable()->default(100)->comment('规则优先级(数字越小优先级越高)');
            $table->string('methods', 50)->nullable()->default('0')->comment('限制值');
            $table->string('path', 50)->nullable()->default('/')->comment('路径');
            $table->string('message', 255)->nullable()->comment('提示信息');
            $table->bigInteger('start_time')->nullable()->comment('开始时间');
            $table->bigInteger('end_time')->nullable()->comment('结束时间');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');
            $table->bigInteger('created_by')->nullable()->comment('创建人');
            $table->bigInteger('updated_by')->nullable()->comment('修改人');
            $table->longText('remark')->nullable()->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_rate_restrictions');
    }
};
