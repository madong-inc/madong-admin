<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitcode.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: https://madong.tech
 */


namespace madong\storage\adapter;

use madong\storage\exception\StorageException;
use madong\storage\interface\FileAdapterInterface;
use madong\storage\traits\ErrorMsgTrait;
use Webman\Http\UploadFile;

abstract class AbstractFileAdapter implements FileAdapterInterface
{
    use ErrorMsgTrait;

    protected const DEFAULT_ALGO = 'md5';

    protected bool $isFileUpload;
    protected string $directorySeparator;
    protected ?array $uploadedFiles;
    protected array $allowedExtensions = [];
    protected array $deniedExtensions = [];
    protected int $singleFileSizeLimit = 0;
    protected int $totalFilesSizeLimit = 0;
    protected int $maxFilesCount = 0;
    protected string $namingAlgorithm = self::DEFAULT_ALGO;
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->initializeConfig($config);
        $this->directorySeparator = DIRECTORY_SEPARATOR === '\\' ? '/' : DIRECTORY_SEPARATOR;
        $this->isFileUpload = $config['is_file_upload'] ?? true;

        if ($this->isFileUpload) {
            $this->uploadedFiles = request()->file();
            $this->validateUploads();
        }
    }

    public function uploadBase64(string $base64, string $extension = 'png', array $options = []): array
    {
        return $this->returnData(false, 'Base64 upload not supported');
    }

    public function uploadServerFile(string $filePath, array $options = []): array
    {
        return $this->returnData(false, 'Server file upload not supported');
    }

    protected function initializeConfig(array $config): void
    {
        $defaultConfig = config('plugin.madong.storage.app.storage');

        $this->allowedExtensions = $config['allowed_extensions'] ?? $defaultConfig['include'] ?? [];
        $this->deniedExtensions = $config['denied_extensions'] ?? $defaultConfig['exclude'] ?? [];
        $this->singleFileSizeLimit = $config['single_file_limit'] ?? $defaultConfig['single_limit'] ?? 0;
        $this->totalFilesSizeLimit = $config['total_files_limit'] ?? $defaultConfig['total_limit'] ?? 0;
        $this->maxFilesCount = $config['max_files_count'] ?? $defaultConfig['nums'] ?? 0;
        $this->namingAlgorithm = $config['naming_algorithm'] ?? self::DEFAULT_ALGO;

        $this->config = array_merge([
            'allowed_extensions' => $this->allowedExtensions,
            'denied_extensions' => $this->deniedExtensions,
            'single_file_limit' => $this->singleFileSizeLimit,
            'total_files_limit' => $this->totalFilesSizeLimit,
            'max_files_count' => $this->maxFilesCount,
            'naming_algorithm' => $this->namingAlgorithm,
        ], $config);

        if (is_callable($this->config['dirname'] ?? null)) {
            $this->config['dirname'] = (string)($this->config['dirname'])();
        }
    }

    protected function validateUploads(): void
    {
        if (empty($this->uploadedFiles)) {
            throw new StorageException('No valid files found for upload');
        }

        $this->validateFilesValidity();
        $this->validateFileExtensions();
        $this->validateFilesSize();
    }

    protected function validateFilesValidity(): void
    {
        foreach ($this->uploadedFiles as $file) {
            if (!$file->isValid()) {
                throw new StorageException('Invalid file: ' . $file->getClientOriginalName());
            }
        }
    }

    protected function validateFileExtensions(): void
    {
        foreach ($this->uploadedFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if (!empty($this->allowedExtensions) && !in_array($extension, $this->allowedExtensions)) {
                throw new StorageException(sprintf(
                    'File extension "%s" is not allowed. Allowed extensions: %s',
                    $extension,
                    implode(', ', $this->allowedExtensions)
                ));
            }

            if (in_array($extension, $this->deniedExtensions)) {
                throw new StorageException(sprintf(
                    'File extension "%s" is explicitly denied',
                    $extension
                ));
            }
        }
    }

    protected function validateFilesSize(): void
    {
        if (count($this->uploadedFiles) > $this->maxFilesCount) {
            throw new StorageException(sprintf(
                'Maximum number of files exceeded. Limit: %d',
                $this->maxFilesCount
            ));
        }

        $totalSize = 0;
        foreach ($this->uploadedFiles as $file) {
            $fileSize = $file->getSize();
            $fileName = $file->getClientOriginalName();

            if ($fileSize > $this->singleFileSizeLimit) {
                throw new StorageException(sprintf(
                    'File "%s" exceeds single file size limit (%d bytes)',
                    $fileName,
                    $this->singleFileSizeLimit
                ));
            }

            $totalSize += $fileSize;
        }

        if ($totalSize > $this->totalFilesSizeLimit) {
            throw new StorageException(sprintf(
                'Total upload size exceeds limit (%d bytes)',
                $this->totalFilesSizeLimit
            ));
        }
    }

    protected function getFileSize(UploadFile $file): int
    {
        return $file->getSize();
    }
}
