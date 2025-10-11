<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_rate_limiter', function (Blueprint $table) {
            $table->comment('限流规则表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->string('name', 64)->comment('规则名称');
            $table->enum('match_type', ['allow', 'exact'])->nullable()->default('exact')->comment('匹配类型');
            $table->string('ip', 50)->nullable()->comment('ip地址');
            $table->integer('priority')->nullable()->default(100)->comment('优先级');
            $table->string('methods', 20)->nullable()->default('GET')->comment('请求方法');
            $table->string('path', 255)->nullable()->default('/')->comment('请求路径');
            $table->string('limit_type', 50)->nullable()->default('count')->comment('限制类型');
            $table->integer('limit_value')->nullable()->default(0)->comment('限制值');
            $table->integer('period')->nullable()->default(60)->comment('统计周期(秒)');
            $table->tinyInteger('enabled')->nullable()->default(1)->comment('状态');
            $table->longText('message')->nullable()->comment('提示信息');
            $table->bigInteger('created_by')->nullable()->comment('创建人');
            $table->bigInteger('updated_by')->nullable()->comment('修改人');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('修改时间');

        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_rate_limiter');
    }
};
