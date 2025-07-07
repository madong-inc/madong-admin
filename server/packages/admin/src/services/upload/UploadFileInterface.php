<?php
namespace madong\admin\services\upload;

interface UploadFileInterface
{
    /**
     * @desc: 上传文件
     *
     * @param array $options
     *
     * @return mixed
     */
    public function uploadFile(array $options): mixed;

    /**
     * @desc: 上传服务端文件
     *
     * @param string $filePath
     *
     * @return mixed
     */
    public function uploadServerFile(string $filePath): mixed;

    /**
     * @desc: Base64上传文件
     *
     * @param string $base64
     * @param string $extension
     *
     * @return mixed
     */
    public function uploadBase64(string $base64, string $extension = 'image'): mixed;
}
