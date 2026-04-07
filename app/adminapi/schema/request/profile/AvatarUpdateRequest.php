<?php

declare(strict_types=1);

namespace app\adminapi\schema\request\profile;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: '头像更新请求',
    description: '更新当前登录用户的头像'
)]
final class AvatarUpdateRequest
{
    #[OA\Property(
        property: 'avatar',
        description: '头像 URL 或 Base64 编码',
        type: 'string',
        example: '/upload/avatar.jpg'
    )]
    public string $avatar;
}
