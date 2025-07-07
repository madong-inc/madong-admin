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

use InvalidArgumentException;
use madong\storage\exception\StorageException;
use OSS\Core\OssException;
use OSS\OssClient;
use SplFileInfo;
use Throwable;

/**
 * 阿里云OSS适配器
 *
 * @author Mr.April
 * @since  1.0
 */
class OssAdapter extends AbstractFileAdapter
{
    protected ?OssClient $client = null;

    public function getClient(): OssClient
    {
        if ($this->client === null) {
            $this->client = new OssClient(
                $this->config['access_key_id'],
                $this->config['access_key_secret'],
                $this->config['endpoint'],
                $this->config['is_cname'] ?? false
            );
        }

        return $this->client;
    }

    public function uploadFile(array $options = []): array
    {
        $results = [];
        foreach ($this->uploadedFiles as $key => $file) {
            $result    = $this->uploadSingleFile($file, $key);
            $results[] = $this->returnData(true, 'File uploaded successfully', 0, $result);
        }

        return $results;
    }

    public function uploadServerFile(string $filePath, array $options = []): array
    {
        try {
            $file = new SplFileInfo($filePath);
            if (!$file->isFile()) {
                return $this->returnData(false, 'Invalid file: ' . $filePath);
            }

            $result = $this->uploadSingleFile($file);
            return $this->returnData(true, 'Server file uploaded', 0, $result);
        } catch (Throwable $e) {
            throw new StorageException('Server file upload failed: ' . $e->getMessage());
        }
    }

    public function uploadBase64(string $base64, string $extension = 'png', array $options = []): array
    {
        try {
            $base64Data = $this->parseBase64Data($base64);
            $tempFile   = $this->createTempFile($base64Data, $extension);
            $file       = new SplFileInfo($tempFile);

            $result = $this->uploadSingleFile($file, null, $extension);
            unlink($tempFile); // Clean up temp file

            return $this->returnData(true, 'Base64 file uploaded', 0, $result);
        } catch (Throwable $e) {
            throw new StorageException('Base64 upload failed: ' . $e->getMessage());
        }
    }

    protected function uploadSingleFile($file, ?string $key = null, ?string $forcedExtension = null): array
    {
        $extension = $forcedExtension ?? $file->getExtension();
        $fileHash  = hash_file($this->namingAlgorithm, $file->getPathname());
        $objectKey = $this->generateObjectKey($fileHash, $extension);

        $response = $this->getClient()->uploadFile(
            $this->config['bucket'],
            $objectKey,
            $file->getPathname()
        );

        $this->validateOssResponse($response);

        return [
            'original_name' => $file->getFilename(),
            'stored_name'   => basename($objectKey),
            'path'          => $objectKey,
            'url'           => $this->generateFileUrl($objectKey),
            'hash'          => $fileHash,
            'size'          => $file->getSize(),
            'extension'     => $extension,
        ];
    }

    protected function validateOssResponse(array $response): void
    {
        if (!isset($response['info']) || $response['info']['http_code'] !== 200) {
            throw new StorageException(
                'OSS upload failed: ' . ($response['info']['status'] ?? 'Unknown error')
            );
        }
    }

    protected function generateObjectKey(string $fileHash, string $extension): string
    {
        return trim($this->config['dirname'], '/')
            . '/'
            . $fileHash
            . '.'
            . $extension;
    }

    protected function generateFileUrl(string $objectKey): string
    {
        return rtrim($this->config['domain'], '/')
            . '/'
            . ltrim($objectKey, '/');
    }

    protected function parseBase64Data(string $base64): string
    {
        $parts = explode(',', $base64);
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Invalid base64 format');
        }
        return base64_decode($parts[1]);
    }

    protected function createTempFile(string $data, string $extension): string
    {
        $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
        file_put_contents($tempPath, $data);
        return $tempPath;
    }
}
