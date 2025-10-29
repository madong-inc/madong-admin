<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_message', function (Blueprint $table) {
            $table->comment('系统消息表');
            $table->bigInteger('id')->unsigned()->primary()->comment('主键');
            $table->string('type', 50)->comment('消息类型参考枚举类');
            $table->string('title', 255)->nullable()->default('')->comment('消息标题（日志类消息可为空）');
            $table->text('content')->comment('消息内容（支持富文本存储）');
            $table->bigInteger('sender_id')->nullable()->default(0)->comment('发送者ID（0表示系统发送）');
            $table->bigInteger('receiver_id')->comment('接收者ID（可关联用户表）');
            $table->enum('status', ['unread', 'read'])->default('unread')->comment('消息状态');
            $table->tinyInteger('priority')->nullable()->default(3)->comment('优先级（1紧急 2急迫 3普通）');
            $table->string('channel', 50)->nullable()->default('message')->comment('发送渠道');
            $table->string('related_id', 100)->nullable()->default('')->comment('关联业务ID（如订单号、日志ID等）');
            $table->string('related_type', 100)->nullable()->default('')->comment('关联业务类型');
            $table->json('jump_params')->nullable()->comment('业务跳转参数');
            $table->string('message_uuid', 50)->nullable()->comment('消息uuid');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('expired_at')->nullable()->comment('过期时间');
            $table->bigInteger('read_at')->nullable()->comment('已读时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');

        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_message');
    }
};
