<?php

namespace core\generator\factory;

use core\generator\interfaces\SceneGeneratorInterface;
use core\generator\interfaces\FileGeneratorInterface;

/**
 * 生成器工厂
 * 负责创建不同类型的生成器
 */
class GeneratorFactory
{
    /**
     * 创建场景生成器
     *
     * @param string $sceneType 场景类型
     * @param array  $config    配置信息
     *
     * @return SceneGeneratorInterface 场景生成器
     * @throws \Exception
     */
    public function createSceneGenerator(string $sceneType, array $config): SceneGeneratorInterface
    {
        $className = "core\\generator\\scene\\" . ucfirst($sceneType) . "SceneGenerator";
        if (!class_exists($className)) {
            throw new \Exception('Scene generator not found: ' . $sceneType);
        }

        return new $className($config);
    }

    /**
     * 创建文件类型生成器
     *
     * @param string $fileType 文件类型
     * @param array  $config   配置信息
     *
     * @return FileGeneratorInterface 文件类型生成器
     */
    public function createFileGenerator(string $fileType, array $config): FileGeneratorInterface
    {
        // 处理特殊文件类型
        $fileTypeMap = [
            'api_model'     => 'ApiModel',
            'view_schema'   => 'ViewSchema',
            'request_form'  => 'RequestForm',
            'request_query' => 'RequestQuery',

        ];

        if (isset($fileTypeMap[$fileType])) {
            $className = "core\\generator\\file\\" . $fileTypeMap[$fileType] . "Generator";
        } else {
            $className = "core\\generator\\file\\" . ucfirst($fileType) . "Generator";
        }

        if (!class_exists($className)) {
            throw new \Exception('File generator not found: ' . $fileType);
        }

        return new $className($config);
    }
}
