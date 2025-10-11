<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_upload', function (Blueprint $table) {
            $table->comment('文件信息表');
            $table->bigInteger('id')->unsigned()->primary()->comment('文件信息ID');
            $table->longText('url')->comment('文件访问地址');
            $table->bigInteger('size')->nullable()->comment('文件大小，单位字节');
            $table->string('size_info', 64)->nullable()->comment('文件大小，有单位');
            $table->string('hash', 64)->nullable()->comment('文件hash');
            $table->string('filename', 255)->nullable()->comment('文件名称');
            $table->string('original_filename', 255)->nullable()->comment('原始文件名');
            $table->longText('base_path')->nullable()->comment('基础存储路径');
            $table->longText('path')->nullable()->comment('存储路径');
            $table->string('ext', 32)->nullable()->comment('文件扩展名');
            $table->string('content_type', 100)->nullable()->comment('MIME类型');
            $table->string('platform', 32)->nullable()->comment('存储平台');
            $table->string('th_url', 255)->nullable()->comment('缩略图访问路径');
            $table->string('th_filename', 255)->nullable()->comment('缩略图大小，单位字节');
            $table->bigInteger('th_size')->nullable()->comment('缩略图大小，单位字节');
            $table->string('th_size_info', 64)->nullable()->comment('缩略图大小，有单位');
            $table->string('th_content_type', 32)->nullable()->comment('缩略图MIME类型');
            $table->string('object_id', 32)->nullable()->comment('文件所属对象id');
            $table->string('object_type', 32)->nullable()->comment('文件所属对象类型，例如用户头像，评价图片');
            $table->text('attr')->nullable()->comment('附加属性');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            $table->bigInteger('created_by')->nullable()->comment('创建用户');
            $table->bigInteger('updated_by')->nullable()->comment('更新用户');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_upload');
    }
};
