<?php
declare(strict_types=1);
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.tech
 */
namespace app\adminapi\validate\generator;

use core\base\BaseValidate;

/**
 * 代码生成器
 * Class Generator
 *
 * @package app\validate\generator
 */
class GeneratorValidate extends BaseValidate
{

    protected array $rule = [
        'table_name' => 'require|max:64',
        'table_content' => 'require|max:64',
    ];

    protected array $message = [
        'table_name.require' => 'validate_generator.table_name_require',
        'table_name.max' => 'validate_generator.table_name_max',
        'table_content.require' => 'validate_generator.table_content_require',
        'table_content.max' => 'validate_generator.table_content_max',
    ];

    protected array $scene = [
        'store' => ['table_name'],
        "update" => ['table_name', 'table_content', 'class_name', 'module_name', 'table_column'],
    ];
}
