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
use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\CosException;
use SplFileInfo;
use Throwable;
use madong\storage\exception\StorageException;


class CosAdapter extends AbstractFileAdapter
{
    protected ?Client $client = null;

    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client([
                'region' => $this->config['region'],
                'schema' => 'https',
                'credentials' => [
                    'secretId' => $this->config['secret_id'],
                    'secretKey' => $this->config['secret_key'],
                ],
            ]);
        }

        return $this->client;
    }

    public function uploadFile(array $options = []): array
    {
        $results = [];
        foreach ($this->uploadedFiles as $key => $file) {
            $result = $this->uploadSingleFile($file, $key);
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
            $tempFile = $this->createTempFile($base64Data, $extension);
            $file = new SplFileInfo($tempFile);

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
        $fileHash = hash_file($this->namingAlgorithm, $file->getPathname());
        $objectKey = $this->generateObjectKey($fileHash, $extension);

        $this->getClient()->putObject([
            'Bucket' => $this->config['bucket'],
            'Key' => $objectKey,
            'Body' => fopen($file->getPathname(), 'rb'),
        ]);

        return [
            'original_name' => $file->getFilename(),
            'stored_name' => basename($objectKey),
            'path' => $objectKey,
            'url' => $this->generateFileUrl($objectKey),
            'hash' => $fileHash,
            'size' => $file->getSize(),
            'extension' => $extension,
        ];
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
