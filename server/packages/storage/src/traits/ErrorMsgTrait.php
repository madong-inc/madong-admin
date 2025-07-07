<?php

namespace madong\storage\traits;


trait ErrorMsgTrait
{
    /**
     * 错误信息结构.
     *
     * @var array{
     *     message: string,
     *     data: array
     * }
     */
    protected array $error = [
        'message' => '',
        'data' => [],
    ];

    /**
     * 设置错误信息.
     *
     * @param bool $success 操作是否成功
     * @param string $message 错误消息
     * @param array $data 附加数据
     * @return bool 返回操作状态
     */
    public function setError(bool $success, string $message, array $data = []): bool
    {
        $this->error = [
            'message' => $message,
            'data' => $data,
        ];

        return $success;
    }

    /**
     * 获取完整的错误信息.
     *
     * @return array{
     *     message: string,
     *     data: array
     * }
     */
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * 获取错误消息.
     */
    public function getMessage(): string
    {
        return $this->error['message'];
    }

    /**
     * 获取附加数据.
     */
    public function getData(): array
    {
        return $this->error['data'];
    }

    /**
     * 标准化响应数据.
     *
     * @param bool $success 操作状态
     * @param string $message 提示消息
     * @param int $code 状态码
     * @param array $data 响应数据
     * @return array{
     *     success: bool,
     *     message: string,
     *     code: int,
     *     data: array
     * }
     */
    public function returnData(bool $success, string $message = '', int $code = 0, array $data = []): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ];
    }
}
