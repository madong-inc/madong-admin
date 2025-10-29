<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_admin', function (Blueprint $table) {
            $table->comment('后台用户信息表');
            $table->bigInteger('id')->primary()->unsigned()->comment('主键ID');
            $table->string('user_name', 50)->unique()->comment('用户名（唯一）');
            $table->string('real_name', 50)->comment('真实姓名');
            $table->string('nick_name', 50)->nullable()->comment('昵称');
            $table->string('password', 255)->comment('密码（加密存储）');
            $table->tinyInteger('is_super')->default(0)->comment('是否超级管理员：1是，0否');
            $table->smallInteger('enabled')->default(1)->comment('是否启用：1是，0否');
            $table->smallInteger('is_locked')->default(0)->comment('是否锁定：1是，0否');
            $table->tinyInteger('sex')->nullable()->comment('性别：1男，2女');
            $table->string('mobile_phone', 20)->nullable()->comment('手机号码');
            $table->string('email', 100)->unique()->nullable()->comment('邮箱（唯一）');
            $table->string('tel', 20)->nullable()->comment('固定电话');
            $table->string('birthday', 20)->nullable()->comment('生日（格式：Y-m-d）');
            $table->string('avatar', 255)->nullable()->comment('头像URL');
            $table->string('signed', 255)->nullable()->comment('个人签名');
            $table->string('dashboard', 255)->nullable()->comment('仪表盘布局配置');
            $table->text('backend_setting')->nullable()->comment('后台个性化设置（JSON格式）');
            $table->longText('remark')->nullable()->comment('备注');
            $table->bigInteger('dept_id')->nullable()->comment('所属部门ID');
            $table->bigInteger('created_by')->nullable()->comment('创建人ID');
            $table->bigInteger('updated_by')->nullable()->comment('最后更新人ID');
            $table->bigInteger('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_at')->nullable()->comment('最后更新时间');
            $table->bigInteger('deleted_at')->nullable()->comment('软删除时间');

            // 索引优化（提升查询效率）
            $table->index('user_name');    // 用户名快速查询
            $table->index('email');       // 邮箱快速查询
            $table->index('dept_id');     // 部门关联查询
            $table->index('created_at');  // 按创建时间排序/筛选
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_admin');
    }
};
