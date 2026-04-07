<?php
declare(strict_types=1);

namespace core\jwt\enum;

/**
 * Token 状态枚举
 */
enum TokenStatus: string
{
    case ACTIVE = 'active';
    case REFRESHED = 'refreshed';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';

    /**
     * 获取枚举值
     */
    public function value(): string
    {
        return $this->value;
    }
}
