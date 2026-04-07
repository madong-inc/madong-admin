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

namespace app\api\controller\system;



use app\api\controller\Base;
use app\api\middleware\ApiAccessTokenMiddleware;
use app\api\schema\request\system\ConfigGroupQueryRequest;
use app\api\schema\request\system\ConfigQueryRequest;
use app\api\schema\request\system\ConfigValueRequest;
use app\service\api\system\ConfigService;
use core\tool\Json;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\AllowAnonymous;
use OpenApi\Attributes as OA;
use support\annotation\Middleware;
use support\Request;
use WebmanTech\Swagger\DTO\SchemaConstants;

//#[Middleware(ApiAccessTokenMiddleware::class)]
final class ConfigController extends Base
{
    public function __construct(ConfigService $service)
    {
        $this->service = $service;
    }

    /**
     * 按分组获取配置
     *
     * @param Request $request
     * @param string  $group_code
     *
     * @return \support\Response
     * @throws \Exception
     */
    #[OA\Get(
        path: "/system/config/group/{group_code}",
        summary: "按分组获取配置",
        tags: ["系统配置"],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => ConfigGroupQueryRequest::class,
        ]
    )]
    #[SimpleResponse(schema: [], example: '{"site_open": "1","site_url": "http://127.0.0.1:8001","site_name": "madong-admin","site_logo": "https://madong.tech/assets/images/logo.svg","site_network_security": "2024042441号-2","site_description": "快速开发框架","site_record_no": "2024042442","site_icp_url": "https://beian.miit.gov.cn/","site_network_security_url": ""}')]
    public function getByGroup(Request $request, string $group_code): \support\Response
    {
        try {
            // 参数验证
            if (empty($group_code)) {
                return Json::fail('分组编码不能为空');
            }

            $options = [
                'enabled_only'  => $request->input('enabled_only', true),
                'with_metadata' => $request->input('with_metadata', false),
            ];
            $result  = $this->service->getByGroup($group_code, [], $options);
            return Json::success('操作成功', $result);
        } catch (\Exception $e) {

            return Json::fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: "/system/config/code/{code}",
        summary: "按code获取配置",
        tags: ["系统配置"],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => ConfigQueryRequest::class,
        ]
    )]
    #[OA\Parameter(
        name: "code",
        description: "配置编码",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "string"),
    )]
    #[SimpleResponse(schema: [], example: ' {"site_open": "1","site_url": "http://127.0.0.1:8001"}')]
    public function getByCode(Request $request, string $code): \support\Response
    {
        try {
            var_dump('执行');
            // 参数验证
            if (empty($code)) {
                return Json::fail('配置编码不能为空');
            }

            $groupCode = $request->input('group_code', '');
            $options   = [];
            if (!empty($groupCode)) {
                $options['group_code'] = $groupCode;
            }
            $result = $this->service->config($code, [], $options);
            return Json::success('操作成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 获取特定配置项的特定键值
     *
     * @param Request $request
     * @param string  $code
     *
     * @return \support\Response
     * @throws \Exception
     */
    #[OA\Get(
        path: "/system/config/{code}/value",
        summary: "获取特定配置项的特定键值",
        tags: ["系统配置"],
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => ConfigValueRequest::class,
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function getValue(Request $request, string $code): \support\Response
    {
        try {
            // 参数验证
            if (empty($code)) {
                return Json::fail('配置项编码不能为空');
            }

            $groupCode = $request->input('group_code');
            $key       = $request->input('key');

            if (empty($groupCode) || empty($key)) {
                return Json::fail('分组编码和配置键不能为空');
            }

            $result = $this->service->getValue($code, $key, null, ['group_code' => $groupCode]);
            return Json::success('操作成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 获取所有配置并按分组整理
     *
     * @param Request $request
     *
     * @return \support\Response
     * @throws \Exception
     */
    #[OA\Get(
        path: "/system/config/all-grouped",
        summary: "获取所有配置并按分组整理",
        tags: ["系统配置"],
    )]
    #[OA\Parameter(
        name: "enabled_only",
        description: "是否只获取启用的配置项",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", default: true),
    )]
    #[OA\Parameter(
        name: "group_filter",
        description: "分组过滤，多个分组用逗号分隔",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string"),
    )]
    #[OA\Parameter(
        name: "with_metadata",
        description: "是否包含配置项的完整元数据",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", default: false),
    )]
    #[OA\Parameter(
        name: "key_by",
        description: "按指定字段作为键名（'code' 或 'id'）",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", enum: ["code", "id"]),
    )]
    #[OA\Parameter(
        name: "sort_groups",
        description: "是否对分组进行排序",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", default: false),
    )]
    #[OA\Parameter(
        name: "sort_configs",
        description: "是否对配置项进行排序",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean", default: false),
    )]
    #[SimpleResponse(schema: [], example: '{"site": {"site_name": "网站名称"}, "oss": {"accessKeyId": "xxx"}}')]
    public function getAllGrouped(Request $request): \support\Response
    {
        try {
            $options = [
                'enabled_only'  => $request->input('enabled_only', true),
                'with_metadata' => $request->input('with_metadata', false),
                'key_by'        => $request->input('key_by', null),
                'sort_groups'   => $request->input('sort_groups', false),
                'sort_configs'  => $request->input('sort_configs', false),
            ];

            // 处理分组过滤参数
            $groupFilter = $request->input('group_filter', '');
            if (!empty($groupFilter)) {
                $options['group_filter'] = explode(',', $groupFilter);
            }

            $result = $this->service->getAllGrouped($options);
            return Json::success('操作成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }
}