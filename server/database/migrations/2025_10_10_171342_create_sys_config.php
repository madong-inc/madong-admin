<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_config', function (Blueprint $table) {
            // 1. 主键：配置ID
            $table->bigInteger('id')
                ->primary()
                ->comment('配置ID');

            // 2. 分组编码（可空，默认NULL）
            $table->string('group_code', 64)
                ->nullable()
                ->default(null)
                ->comment('分组编码');

            // 3. 唯一编码（非空）
            $table->string('code', 64)->comment('唯一编码');

            // 4. 配置名称（非空）
            $table->string('name', 64)->comment('配置名称');

            // 5. 配置内容（长文本，可空）
            $table->longText('content')
                ->nullable()
                ->comment('配置内容');

            // 6. 是否系统（tinyint，可空，默认0）
            $table->tinyInteger('is_sys')
                ->nullable()
                ->default(0)
                ->comment('是否系统');

            // 7. 是否启用（tinyint，可空，默认1）
            $table->tinyInteger('enabled')
                ->nullable()
                ->default(1)
                ->comment('是否启用');

            // 8. 创建时间（bigint，可空）
            $table->bigInteger('created_at')
                ->nullable()
                ->comment('创建时间');

            // 9. 创建用户（bigint，可空）
            $table->bigInteger('created_by')
                ->nullable()
                ->comment('创建用户');

            // 10. 更新时间（bigint，可空）
            $table->bigInteger('updated_at')
                ->nullable()
                ->comment('更新时间');

            // 11. 更新用户（bigint，可空）
            $table->bigInteger('updated_by')
                ->nullable()
                ->comment('更新用户');

            // 12. 删除时间（bigint，可空，软删除标记）
            $table->bigInteger('deleted_at')
                ->nullable()
                ->comment('是否删除');

            // 13. 备注（长文本，可空）
            $table->longText('remark')
                ->nullable()
                ->comment('备注');
        });
    }

    public function down(Builder $schema): void
    {
        // 回滚：删除表（表名：sys_config）
        $schema->dropIfExists('sys_config');
    }
};
