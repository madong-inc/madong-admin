<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_dept', function (Blueprint $table) {
            // 1. 主键：部门ID
            $table->bigInteger('id')
                ->unsigned()
                ->primary()
                ->comment('主键');

            // 2. 父部门ID（可空）
            $table->bigInteger('pid')
                ->unsigned()
                ->nullable()
                ->default(0)
                ->comment('父ID');

            // 3. 组级集合（可空）
            $table->string('level', 500)
                ->nullable()
                ->default(null)
                ->comment('组级集合');

            // 4. 部门唯一编码（可空）
            $table->string('code', 50)
                ->nullable()
                ->default(null)
                ->comment('部门唯一编码');

            // 5. 部门名称（可空）
            $table->string('name', 30)
                ->nullable()
                ->default(null)
                ->comment('部门名称');

            // 6. 负责人（可空）
            $table->string('main_leader_id', 20)
                ->nullable()
                ->default(null)
                ->comment('负责人');

            // 7. 联系电话（可空）
            $table->string('phone', 11)
                ->nullable()
                ->default(null)
                ->comment('联系电话');

            // 8. 部门状态（可空，默认1：正常；0：停用）
            $table->smallInteger('enabled')
                ->nullable()
                ->default(1)
                ->comment('状态 (1正常 0停用)');

            // 9. 排序（可空，默认0）
            $table->smallInteger('sort')
                ->unsigned()
                ->nullable()
                ->default(0)
                ->comment('排序');

            // 10. 创建者（可空）
            $table->bigInteger('created_by')
                ->nullable()
                ->comment('创建者');

            // 11. 更新者（可空）
            $table->bigInteger('updated_by')
                ->nullable()
                ->comment('更新者');

            // 12. 创建时间（可空）
            $table->bigInteger('created_at')
                ->nullable()
                ->comment('创建时间');

            // 13. 修改时间（可空）
            $table->bigInteger('updated_at')
                ->nullable()
                ->comment('修改时间');

            // 14. 删除时间（可空）
            $table->bigInteger('deleted_at')
                ->nullable()
                ->comment('删除时间');

            // 15. 备注（长文本，可空）
            $table->longText('remark')
                ->nullable()
                ->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_dept）
        $schema->dropIfExists('sys_dept');
    }
};
