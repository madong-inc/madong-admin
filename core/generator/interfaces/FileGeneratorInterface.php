<?php

namespace core\generator\interfaces;

/**
 * 文件类型生成器接口
 * 负责生成特定类型的文件内容
 */
interface FileGeneratorInterface
{
    /**
     * 生成文件内容
     * @return string 文件内容
     */
    public function generateContent(): string;

    /**
     * 获取文件扩展名
     * @return string 文件扩展名
     */
    public function getFileExtension(): string;
}
