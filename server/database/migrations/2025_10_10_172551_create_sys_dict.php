<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_dict', function (Blueprint $table) {
            // 1. 主键：字典ID
            $table->bigInteger('id')
                ->unsigned()
                ->primary()
                ->comment('主键');

            // 2. 字典类型（可空）
            $table->string('group_code', 50)
                ->nullable()
                ->default(null)
                ->comment('字典类型');

            // 3. 字典名称（可空）
            $table->string('name', 50)
                ->nullable()
                ->default(null)
                ->comment('字典名称');

            // 4. 字典标识（可空）
            $table->string('code', 100)
                ->nullable()
                ->default(null)
                ->comment('字典标示');

            // 5. 排序（可空，默认0）
            $table->bigInteger('sort')
                ->nullable()
                ->default(0)
                ->comment('排序');

            // 6. 数据类型（可空，默认1）
            $table->smallInteger('data_type')
                ->nullable()
                ->default(1)
                ->comment('数据类型');

            // 7. 描述（长文本，可空）
            $table->longText('description')
                ->nullable()
                ->comment('描述');

            // 8. 状态（可空，默认1：正常；0：停用）
            $table->smallInteger('enabled')
                ->nullable()
                ->default(1)
                ->comment('状态 (1正常 0停用)');

            // 9. 创建者（可空）
            $table->bigInteger('created_by')
                ->nullable()
                ->comment('创建者');

            // 10. 更新者（可空）
            $table->bigInteger('updated_by')
                ->nullable()
                ->comment('更新者');

            // 11. 创建时间（可空）
            $table->bigInteger('created_at')
                ->nullable()
                ->comment('创建时间');

            // 12. 修改时间（可空）
            $table->bigInteger('updated_at')
                ->nullable()
                ->comment('修改时间');

            // 13. 删除时间（可空）
            $table->bigInteger('deleted_at')
                ->nullable()
                ->comment('删除时间');
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_dict');
    }
};
