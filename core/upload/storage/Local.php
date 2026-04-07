<?php

namespace core\upload\storage;

use core\exception\handler\UploadException;
use core\upload\BaseUpload;

/**
 * 本地上传
 *
 * @author Mr.April
 * @since  1.0
 */
class Local extends BaseUpload
{
    public function uploadFile(array $options = []): array
    {
        $result   = [];
        $root     = $this->getRootPath();
        $rootDir  = $this->config['root_dir'] ?? $this->config['dirname'] ?? ''; // 根目录
        $basePath = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!$this->createDir($basePath)) {
            throw new UploadException('文件夹创建失败，请核查是否有对应权限。');
        }
        $domain = rtrim($this->config['domain'] ?? '', '/');
        foreach ($this->files as $key => $file) {
            $uniqueId     = $this->getUniqueId($file->getPathname());
            $saveFilename = $uniqueId . '.' . $file->getUploadExtension();
            $savePath     = $basePath . $saveFilename;
            $url          = $domain . $this->dirSeparator . $saveFilename;
            $basePathUrl  = $this->dirSeparator . $saveFilename;
            
            if (!empty($rootDir)) {
                // 生成子目录
                $subDir = $this->getSubdir($options);
                $fullDir = $rootDir . ($subDir ? $this->dirSeparator . $subDir : '');
                $savePath = $root . $this->dirSeparator . $fullDir . $this->dirSeparator . $saveFilename;
                $url      = $domain . $this->dirSeparator . $fullDir . $this->dirSeparator . $saveFilename;
                $basePathUrl = $this->dirSeparator . $fullDir . $this->dirSeparator . $saveFilename;
            }
            
            // 确保目录存在
            $dirPath = dirname($savePath);
            if (!is_dir($dirPath) && !$this->createDir($dirPath)) {
                throw new UploadException('子目录创建失败，请核查是否有对应权限。');
            }
            
            $temp = [
                'key'         => $key,
                'origin_name' => $file->getUploadName(),
                'save_name'   => $saveFilename,
                'save_path'   => $savePath,
                'url'         => $url,
                'unique_id'   => $uniqueId,
                'size'        => $file->getSize(),
                'mime_type'   => $file->getUploadMimeType(),
                'extension'   => $file->getUploadExtension(),
                'base_path'   => $basePathUrl
            ];
            $file->move($savePath);
            $result[] = $temp;
        }
        return $result;
    }
    
    /**
     * 获取子目录
     */
    protected function getSubdir(array $options = []): string
    {
        // 优先使用 options 中的子目录（支持 sub_dir 和 subdirectory、subdir 两种键名）
        if (isset($options['sub_dir']) && !empty($options['sub_dir'])) {
            return (string)$options['sub_dir'];
        } elseif (isset($options['subdirectory']) && !empty($options['subdirectory'])) {
            return (string)$options['subdirectory'];
        } elseif (isset($options['subdir']) && !empty($options['subdir'])) {
            return (string)$options['subdir'];
        }
        
        // 其次使用配置中的 sub_dir 作为子目录
        if (isset($this->config['sub_dir']) && !empty($this->config['sub_dir'])) {
            return (string)$this->config['sub_dir'];
        } elseif (isset($this->config['directory']) && !empty($this->config['directory'])) {
            return (string)$this->config['directory'];
        }
        
        // 默认使用年月格式 YYYYMM
        $subdirFormat = $this->config['subdir_format'] ?? 'Ym';
        
        return match ($subdirFormat) {
            'Ymd' => date('Ymd'),
            'Ym' => date('Ym'),
            default => date('Ym')
        };
    }

    protected function createDir(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }
        $parent = dirname($path);
        if (!is_dir($parent) && !$this->createDir($parent)) {
            return false;
        }
        return mkdir($path, 0755, true);
    }

    private function getRootPath(): string
    {
        $root = $this->config['root'] ?? '';
        return match ($root) {
            'public' => public_path(),
            'runtime' => runtime_path(),
            'default' => runtime_path(),
        };
    }

    function uploadServerFile(string $filePath): mixed
    {
        // TODO: Implement uploadServerFile() method.
    }

    public function uploadBase64(string $base64, string $extension = 'JPEG'): mixed
    {
        // TODO: Implement uploadBase64() method.
    }
}
