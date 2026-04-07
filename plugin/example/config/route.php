<?php
/**
 * This file is part of webman.
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;
use WebmanTech\Swagger\Swagger;
use OpenApi\Annotations as OA;


/**
 * 注册example APP路由
 */
Route::group('/example', function () {

    // Swagger 文档路由配置
    Swagger::create()->registerRoute([
        'route_prefix'   => '/openapi',
        'register_route' => true,
        'openapi_doc'    => [
            'scan_path'  => [
                base_path('plugin/example'),
            ],
            'exclude'    => [
                // 排除非控制器文件和目录
            ],
            'processors' => [
                // 添加自定义处理器来过滤基类
            ],
            'modify'     => function (OA\OpenApi $openapi) {
                $openapi->info->title   = config('app.name') . ' API';
                $openapi->info->version = '1.0.0';
                $openapi->servers       = [
                    new OA\Server(
                        [
                            'url'         => '/example',
                            'description' => request()->host(),
                        ]
                    ),
                ];
                /** @phpstan-ignore-next-line */
                if (!$openapi->components instanceof OA\Components) {
                    $openapi->components = new OA\Components([]);
                }
                $openapi->components->securitySchemes = [
                    new OA\SecurityScheme([
                        'securityScheme' => 'api_key',
                        'type'           => 'apiKey',
                        'name'           => config('core.jwt.app.token_name', 'Authorization'),
                        'in'             => 'header',
                    ]),
                ];

                // 过滤掉不应该出现在文档中的路径
                // foreach ($openapi->paths as $path => $pathItem) {
                //     // 过滤掉测试路径
                //     if (str_contains($path, '/test/')) {
                //         unset($openapi->paths[$path]);
                //     }
                // }
            },

        ],
    ]);

    // 注意：ResponseTestController 的路由已通过 Swagger 注解自动生成
    // 无需在此手动定义路由
    // Swagger 会根据 @OA\Get 注解自动注册路由到 /adminapi/test/response/*
});
