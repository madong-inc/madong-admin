<?php

namespace madong\storage\interface;

interface FileAdapterInterface
{
    /**
     * 上传本地文件.
     *
     * @param array{
     *     path: string,
     *     name?: string,
     *     mime?: string,
     *     metadata?: array
     * } $options 上传配置
     * @return array{
     *     success: bool,
     *     path: string,
     *     url: string,
     *     size?: int,
     *     metadata?: array
     * } 返回标准化上传结果
     * @throws FileUploadException 上传失败时抛出异常
     */
    public function uploadFile(array $options): array;

    /**
     * 上传服务端已有文件.
     *
     * @param string $filePath 服务器文件绝对路径
     * @param array $options 附加选项（如重命名、覆盖策略等）
     * @return array 标准化结果（结构同 uploadFile）
     * @throws FileNotFoundException|FileUploadException
     */
    public function uploadServerFile(string $filePath, array $options = []): array;

    /**
     * 上传Base64编码文件.
     *
     * @param string $base64 文件Base64数据（需包含头信息）
     * @param string $extension 文件扩展名（不含点）
     * @param array $options 附加选项
     * @return array 标准化结果（结构同 uploadFile）
     * @throws InvalidBase64Exception|FileUploadException
     */
    public function uploadBase64(
        string $base64,
        string $extension = 'png',
        array $options = []
    ): array;
}
