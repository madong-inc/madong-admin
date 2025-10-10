<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        // 创建数据表（表名：cache）
        $schema->create('cache', function (Blueprint $table) {
            // 主键：缓存键（唯一，varchar(36)）
            $table->string('key', 36)->unique()->comment('缓存键（唯一标识）');

            // 缓存内容（text类型，支持大文本）
            $table->text()->comment('缓存内容');

            // 过期时间（Unix时间戳，int(11)）
            $table->integer('expire_time')->comment('过期时间（Unix时间戳，秒级）');

            // 创建时间（Unix时间戳，int(11)）
            $table->integer('create_time')->comment('创建时间（Unix时间戳，秒级）');

            // 索引优化：加速过期缓存清理
            $table->index('expire_time');

             $table->index('create_time');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：cache）
        $schema->dropIfExists('cache');
    }
};
