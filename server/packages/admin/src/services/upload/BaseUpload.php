<?php

namespace madong\admin\services\upload;

use madong\admin\ex\UploadException;
use Webman\Http\UploadFile;

abstract class BaseUpload implements UploadFileInterface
{

    protected bool $_isFileUpload;
    protected string $dirSeparator = '/';
    protected array $files = [];
    protected array $includes = [];
    protected array $excludes = [];
    protected int $singleLimit = 0;
    protected int $totalLimit = 0;
    protected int $nums = 0;
    protected array $config = [];
    protected string $algo = 'md5';

    public function __construct(array $config = [])
    {
        $this->loadConfig($config);
        $this->_isFileUpload = $config['_is_file_upload'] ?? true;

        if ($this->_isFileUpload) {
            $this->files = request()->file();
            $this->verify();
        }
    }

    abstract function uploadFile(array $options): mixed;

    abstract function uploadServerFile(string $filePath): mixed;

    abstract public function uploadBase64(string $base64, string $extension = 'JPEG'): mixed;

    protected function loadConfig(array $config): void
    {
        $this->config = $config;
        if (isset($this->config['dirname']) && is_callable($this->config['dirname'])) {
            $this->config['dirname'] = (string)$this->config['dirname']() ?: $this->config['dirname'];
        }
    }

    /**
     * 文件校验
     */
    protected function verify(): void
    {
        if (empty($this->files)) {
            throw new UploadException('未找到符合条件的文件资源');
        }
        foreach ($this->files as $file) {
            if (!$file->isValid()) {
                throw new UploadException('未选择文件或者无效的文件');
            }
        }
    }

    /**
     * 文件大小
     *
     * @param \Webman\Http\UploadFile $file
     *
     * @return int
     */
    protected function getSize(UploadFile $file): int
    {
        return $file->getSize();
    }

    /**
     * 计算文件哈希值
     *
     * @param string $pathname
     *
     * @return string
     */
    protected function getUniqueId(string $pathname): string
    {
        return hash_file($this->algo, $pathname);
    }
}
