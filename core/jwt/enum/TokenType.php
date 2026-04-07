<?php
declare(strict_types=1);

namespace core\jwt\enum;

/**
 * Token 类型枚举
 */
enum TokenType: string
{
    case ACCESS = 'access';
    case REFRESH = 'refresh';

    /**
     * 获取枚举值
     */
    public function value(): string
    {
        return $this->value;
    }
}
