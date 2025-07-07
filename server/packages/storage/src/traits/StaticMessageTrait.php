<?php

namespace madong\storage\traits;

trait StaticMessageTrait
{
    /**
     * 静态消息文本.
     */
    protected static string $staticMessage = 'success';

    /**
     * 设置静态消息并返回操作状态.
     *
     * @param bool $success 是否成功
     * @param string $message 消息内容
     * @return bool 返回传入的 $success 状态
     */
    public static function setMessage(bool $success, string $message): bool
    {
        self::$staticMessage = $message;
        return $success;
    }

    /**
     * 获取当前静态消息.
     */
    public static function getMessage(): string
    {
        return self::$staticMessage;
    }

    /**
     * 重置消息为默认值.
     */
    public static function resetMessage(): void
    {
        self::$staticMessage = 'success';
    }
}
