<?php
declare(strict_types=1);

namespace core\jwt\ex;

/**
 * JWT V2 模块基础异常
 */
class JwtException extends \Exception
{
    public function __construct(string $message = '', int $code = -1, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
