<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

return new class {

    public function up(Builder $schema): void
    {
        $schema->create('sys_casbin_rule', function (Blueprint $table) {
            $table->comment('Casbin策略规则表');
            $table->bigInteger('id')->unsigned()->primary()->comment('ID');
            // 策略类型（ptype）、主体（v0）、域（v1）、资源（v2）、动作（v3）、扩展字段（v4-v5）
            $fields = [
                'ptype' => '策略类型',
                'v0'    => '主体(subject)',
                'v1'    => '域(domain)',
                'v2'    => '资源(resource)',
                'v3'    => '动作(action)',
                'v4'    => '扩展字段1',
                'v5'    => '扩展字段2',
            ];

            foreach ($fields as $fieldName => $comment) {
                $table->string($fieldName, 128)
                    ->default('')                     // 默认空字符串
                    ->comment($comment);                    // 字段注释
            }

            // 索引配置
            foreach (array_keys($fields) as $field) {
                $indexName = 'idx_' . $field; // 索引名：idx_ptype / idx_v0 等
                $table->index($field, $indexName); // 创建普通索引（BTREE）
            }
        });
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_casbin_rule');
    }
};
