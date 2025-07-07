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
use Workerman\Psr7\UploadedFile;

class LocalAdapter extends AbstractFileAdapter
{
    public function uploadFile(array $options = []): array
    {
        if (empty($this->uploadedFiles)) {
            return $this->returnData(false, 'No files to upload');
        }

        $basePath = $this->prepareStoragePath();
        $baseUrl = $this->generateBaseUrl();

        $results = [];
        foreach ($this->uploadedFiles as $key => $file) {
            $result = $this->processSingleFile($file, $basePath, $baseUrl);
            $results[] = $this->returnData(true, 'File uploaded successfully', 0, $result);
        }

        return $results;
    }

    protected function prepareStoragePath(): string
    {
        $path = $this->config['root'] . $this->config['dirname'] . $this->directorySeparator;

        if (!$this->createDirectory($path)) {
            throw new StorageException(
                sprintf('Failed to create directory at: %s. Check permissions.', $path)
            );
        }

        return $path;
    }

    protected function generateBaseUrl(): string
    {
        return $this->config['domain']
            . $this->config['uri']
            . str_replace($this->directorySeparator, '/', $this->config['dirname'])
            . '/';
    }

    protected function processSingleFile(
        UploadedFile $file,
        string $storagePath,
        string $baseUrl
    ): array {
        $uniqueId = hash_file($this->namingAlgorithm, $file->getPathname());
        $extension = $file->getClientOriginalExtension();
        $filename = $uniqueId . '.' . $extension;

        $destination = $storagePath . $filename;
        $file->move($destination);

        return [
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $filename,
            'path' => $destination,
            'url' => $baseUrl . $filename,
            'hash' => $uniqueId,
            'size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'extension' => $extension,
        ];
    }

    protected function createDirectory(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }

        $parentDir = dirname($path);
        if (!is_dir($parentDir) && !$this->createDirectory($parentDir)) {
            return false;
        }

        return mkdir($path, 0755, true);
    }
}
