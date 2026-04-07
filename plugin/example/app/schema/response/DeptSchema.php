<?php

declare(strict_types=1);

namespace plugin\example\app\schema\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: '部门树响应模型',
    description: '部门树形结构，支持无限层级'
)]
class DeptSchema
{
    #[OA\Property(description: '部门ID', example: 1)]
    public int $id;

    #[OA\Property(description: '部门名称', example: '技术部')]
    public string $name;

    #[OA\Property(
        description: '子部门列表（树形结构）',
        type: 'array',
        items: new OA\Items(
            ref: '#/components/schemas/DeptSchema'
        )
    )]
    public array $children;
}
