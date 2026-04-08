<?php
declare(strict_types=1);

namespace app\api\controller\member;

use app\api\controller\Base;
use app\api\CurrentMember;
use app\api\event\MemberInfoFetchedEvent;
use app\service\api\member\MemberService;
use core\exception\handler\UnauthorizedHttpException;
use core\tool\Json;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\AllowAnonymous;
use OpenApi\Attributes as OA;
use support\Container;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\Http\UploadFile as WebmanUploadFile;

/**
 * 会员用户控制器
 */
#[OA\Tag(name: '会员模块')]
final class MemberController extends Base
{
    public function __construct(MemberService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: '/member/user-info',
        summary: '获取会员基本信息',
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '获取成功'),
            new OA\Response(response: 401, description: '未登录'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function getProfile(): Response
    {
        try {
            /** @var CurrentMember $currentMember */
            $currentMember = Container::make(CurrentMember::class);
            $data          = $currentMember->user(true);
            if (empty($data)) {
                throw new UnauthorizedHttpException('用户凭证失效请重新登录');
            }

            $permissions = [];
            $extra       = [];

            $event = new MemberInfoFetchedEvent($data['id'], $data, $permissions, $extra);
            $event->dispatch();

            // 将修改后的数据回写到原变量
            $data['permissions'] = $event->getPermissions();

            if (!empty($event->getExtra())) {
                $data = array_merge($data, $event->getExtra());
            }

            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), null, 401);
        }
    }

    #[OA\Put(
        path: '/member/user/update',
        summary: '更新会员信息',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nickname', description: '昵称', type: 'string'),
                    new OA\Property(property: 'avatar', description: '头像', type: 'string'),
                    new OA\Property(property: 'gender', description: '性别', type: 'integer'),
                    new OA\Property(property: 'birthday', description: '生日', type: 'string'),
                ]
            )
        ),
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '更新成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 400, description: '参数错误'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function update(Request $request): Response
    {
        try {
            $data = $request->all();
            $this->service->updateInfo($data);
            return Json::success();
        } catch (\Exception $exception) {
            return Json::fail($exception->getMessage());
        }

    }

    #[OA\Post(
        path: '/member/user/avatar',
        summary: '上传头像',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'file',
                            description: '头像文件',
                            type: 'string',
                            format: 'binary'
                        ),
                    ]
                )
            )
        ),
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '上传成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 400, description: '参数错误'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function uploadAvatar(Request $request): Response
    {
        try {
            $file = $request->file('file');
            if (!$file || !$file instanceof WebmanUploadFile) {
                throw new \Exception('请上传头像文件', 400);
            }

            $result = $this->service->uploadAvatar($file);
            return Json::success('上传成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode() ?: -1);
        }
    }

    #[OA\Put(
        path: '/member/user/bind-phone',
        summary: '绑定会员手机号',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['phone', 'verify_code'],
                properties: [
                    new OA\Property(property: 'phone', description: '手机号', type: 'string'),
                    new OA\Property(property: 'verify_code', description: '验证码', type: 'string'),
                ]
            )
        ),
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '绑定成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 400, description: '参数错误'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function bindPhone(Request $request): Response
    {
        $data   = $request->post();
        $result = $this->service->bindPhone($data);
        return json($result);
    }

    #[OA\Put(
        path: '/member/user/change-password',
        summary: '修改密码',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['old_password', 'new_password'],
                properties: [
                    new OA\Property(property: 'old_password', description: '旧密码', type: 'string'),
                    new OA\Property(property: 'new_password', description: '新密码', type: 'string'),
                ]
            )
        ),
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '修改成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 400, description: '参数错误'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function changePassword(Request $request): Response
    {
        try {
            $data = $request->all();
            $this->service->changePassword($data);
            return Json::success('密码修改成功');
        } catch (\Exception $e) {
            return Json::fail($e->getMessage(), [], $e->getCode() ?: -1);
        }
    }

    #[OA\Get(
        path: '/member/user/address/list',
        summary: '获取地址列表',
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '获取成功'),
            new OA\Response(response: 401, description: '未登录'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function getAddressList(): Response
    {
        $result = $this->service->getAddressList();
        return json($result);
    }

    #[OA\Post(
        path: '/member/user/address/create',
        summary: '创建地址',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'phone', 'province', 'city', 'district', 'address'],
                properties: [
                    new OA\Property(property: 'name', description: '收件人姓名', type: 'string'),
                    new OA\Property(property: 'phone', description: '手机号', type: 'string'),
                    new OA\Property(property: 'province', description: '省份', type: 'string'),
                    new OA\Property(property: 'city', description: '城市', type: 'string'),
                    new OA\Property(property: 'district', description: '区域', type: 'string'),
                    new OA\Property(property: 'address', description: '详细地址', type: 'string'),
                    new OA\Property(property: 'is_default', description: '是否默认地址', type: 'boolean'),
                ]
            )
        ),
        tags: ['会员模块'],
        responses: [
            new OA\Response(response: 200, description: '创建成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 400, description: '参数错误'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function createAddress(Request $request): Response
    {
        $data   = $request->post();
        $result = $this->service->createAddress($data);
        return json($result);
    }

    #[OA\Put(
        path: '/member/user/address/update/{id}',
        summary: '更新地址',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', description: '收件人姓名', type: 'string'),
                    new OA\Property(property: 'phone', description: '手机号', type: 'string'),
                    new OA\Property(property: 'province', description: '省份', type: 'string'),
                    new OA\Property(property: 'city', description: '城市', type: 'string'),
                    new OA\Property(property: 'district', description: '区域', type: 'string'),
                    new OA\Property(property: 'address', description: '详细地址', type: 'string'),
                    new OA\Property(property: 'is_default', description: '是否默认地址', type: 'boolean'),
                ]
            )
        ),
        tags: ['会员模块'],
        parameters: [
            new OA\Parameter(name: 'id', description: '地址ID', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: '更新成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 400, description: '参数错误'),
            new OA\Response(response: 404, description: '地址不存在'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    public function updateAddress(int $id, Request $request): Response
    {
        $data   = $request->post();
        $result = $this->service->updateAddress($id, $data);
        return json($result);
    }

    #[OA\Delete(
        path: '/member/user/address/delete/{id}',
        summary: '删除地址',
        tags: ['会员模块'],
        parameters: [
            new OA\Parameter(name: 'id', description: '地址ID', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: '删除成功'),
            new OA\Response(response: 401, description: '未登录'),
            new OA\Response(response: 404, description: '地址不存在'),
        ]
    )]
    #[SimpleResponse(schema: [], example: [])]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function deleteAddress(int $id): Response
    {
        $result = $this->service->deleteAddress($id);
        return json($result);
    }

}
