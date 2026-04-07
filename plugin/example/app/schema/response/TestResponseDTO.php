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

namespace plugin\example\app\schema\response;

use madong\swagger\schema\BaseResponseDTO;
use OpenApi\Attributes as OA;

/**
 * 测试响应 DTO
 *
 * 继承 BaseResponseDTO 可以通过 x 参数传递 schema
 * 使用方式：x: [SchemaConstants::X_SCHEMA_RESPONSE => TestResponseDTO::class]
 */
#[OA\Schema(
    title: '测试响应DTO',
    description: '测试响应DTO的数据结构'
)]
class TestResponseDTO extends BaseResponseDTO
{
    #[OA\Property(
        property: 'id',
        description: 'ID',
        type: 'string',
        example: '1'
    )]
    public string $id;

    #[OA\Property(
        property: 'username',
        description: '用户名',
        type: 'string',
        example: 'admin'
    )]
    public string $username;

    #[OA\Property(
        property: 'email',
        description: '邮箱',
        type: 'string',
        example: 'admin@example.com'
    )]
    public string $email;

    #[OA\Property(
        property: 'status',
        description: '状态',
        type: 'integer',
        example: 1
    )]
    public int $status;

    #[OA\Property(
        property: 'created_at',
        description: '创建时间',
        type: 'string',
        format: 'date-time',
        example: '2024-01-01T00:00:00+08:00'
    )]
    public string $created_at;
}
