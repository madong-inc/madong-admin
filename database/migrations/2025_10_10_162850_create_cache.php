<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('cache', function (Blueprint $table) {
            $table->comment('缓存表');
            $table->string('key', 36)->unique()->comment('缓存键（唯一标识）');
            $table->text('result')->comment('缓存内容');
            $table->integer('expire_time')->comment('过期时间（Unix时间戳，秒级）');
            $table->integer('create_time')->comment('创建时间（Unix时间戳，秒级）');
            $table->index('expire_time');
            $table->index('create_time');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('cache');
    }
};
