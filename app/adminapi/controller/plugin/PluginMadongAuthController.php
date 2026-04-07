<?php

declare(strict_types=1);
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.tech
 */

namespace app\adminapi\controller\plugin;

use app\adminapi\controller\Base;
use app\adminapi\middleware\AccessTokenMiddleware;
use app\adminapi\middleware\OperationMiddleware;
use app\adminapi\middleware\PermissionMiddleware;
use app\service\core\plugin\PluginRemoteService;
use core\tool\Json;
use madong\swagger\annotation\auth\AllowAnonymous;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\Permission;
use OpenApi\Attributes as OA;
use support\annotation\Middleware;
use support\Request;

#[Middleware(AccessTokenMiddleware::class, PermissionMiddleware::class, OperationMiddleware::class)]
final class PluginMadongAuthController extends Base
{

    public function __construct(PluginRemoteService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: '/madong/auth-info',
        summary: '获取插件授权信息',
        tags: ['应用授权']
    )]
    #[Permission('madong:delegation:read')]
    #[AllowAnonymous(requireToken: true, requirePermission: true)]
    #[SimpleResponse(example: '{"code": 0,"msg": "ok","data": {"company_name": "-","domain": "-","auth_code": "202601***********888888"}}')]
    public function read(Request $request): \support\Response
    {

        $data = [
            'company_name' => '-',
            'domain'       => '-',
            'auth_code'    => config('madong.auth_code'),
        ];
        return Json::success($data);
    }

    #[OA\Post(
        path: '/madong/auth-info',
        summary: '设置插件授权信息',
        tags: ['应用授权']
    )]
    #[Permission('madong:delegation:setting')]
    #[SimpleResponse(schema: [], example: [])]
    public function store(Request $request): \support\Response
    {
        try {
            $param = $request->all();
            if (empty($param['auth_code'])) {
                throw new \Exception('auth_code 不能为空');
            }
            if (empty($param['auth_secret'])) {
                throw new \Exception('auth_secret 不能为空');
            }
            $this->service->verifyRemoteAuthorization($param['auth_code'],$param['auth_secret']);
            //追加到系统配置
            $this->updateMadongConfig($param['auth_code'], $param['auth_secret']);

            return Json::success('授权设置成功');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }

    }

    /**
     * 更新server/config/madong.php文件中的配置
     *
     * @param string $authCode   授权码
     * @param string $authSecret 授权密钥
     *
     * @throws \Exception
     */
    private function updateMadongConfig(string $authCode, string $authSecret): void
    {
        $configPath = base_path() . '/config/madong.php';
        if (!file_exists($configPath)) {
            throw new \Exception('madong.php配置文件不存在');
        }

        $configContent = file_get_contents($configPath);

        // 定义需要替换的参数映射
        $replacements = [
            'auth_code' => $authCode,
            'auth_secret' => $authSecret,
        ];

        foreach ($replacements as $key => $value) {
            $pattern     = '/\'' . $key . '\'\s*=>\s*env\(\'madong\..*?\'\s*,\s*\'(.*?)\'\)/';
            $replacement = "'$key' => env('madong." . ($key === 'auth_code' ? 'code' : 'secret') . "', '$value')";
            $configContent = preg_replace($pattern, $replacement, $configContent);
        }

        file_put_contents($configPath, $configContent);
    }

}