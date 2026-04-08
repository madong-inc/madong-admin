<?php
declare(strict_types=1);

namespace app\api\controller\site;

use app\api\controller\Base;
use app\service\api\web\MenuService;
use core\tool\Json;
use madong\swagger\annotation\response\SimpleResponse;
use madong\swagger\attribute\AllowAnonymous;
use OpenApi\Attributes as OA;
use Webman\Http\Response;

#[OA\Tag(name: '导航菜单模块')]
final class MenuController extends Base
{
    public function __construct(MenuService $service)
    {
        $this->service = $service;
    }

    /**
     * 获取导航菜单列表
     *
     * @return Response
     */
    #[OA\Get(
        path: '/site/nav',
        summary: '导航菜单',
        tags: ['导航菜单模块'],
        responses: [
            new OA\Response(response: 200, description: '获取成功'),
        ]
    )]
    #[SimpleResponse(schema: [], example: '[]')]
    #[AllowAnonymous(requireToken: false, requirePermission: false, description: '公共接口')]
    public function index(): Response
    {
        try {
            $result = $this->service->getNavigationList();
            return Json::success('获取成功', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }
}
