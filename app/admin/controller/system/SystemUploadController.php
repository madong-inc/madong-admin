<?php
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

namespace app\admin\controller\system;

use app\admin\controller\Crud;
use app\services\system\SystemUploadService;
use madong\exception\AdminException;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemUploadController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SystemUploadService::class);
    }

    /**
     * 下载图片到服务器
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function downloadNetworkImage(Request $request): \support\Response
    {
        $url    = $request->input('url', '');
        $result = $this->service->saveNetworkImage($url);
        return Json::success('操作成功', $result);
    }

    /**
     * 根据id下载资源
     *
     * @param \support\Request $request
     *
     * @return \support\Response|\Webman\Http\Response
     */
    public function downloadResourceById(Request $request)
    {
        try {
            $id   = $request->route->param('id');
            $data = $this->service->get($id);
            if (empty($data)) {
                throw new AdminException('数据未找到', -1);
            }
            return response()->download($data->path,$data->filename);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 根据hash下载资源
     *
     * @param \support\Request $request
     *
     * @return \support\Response|\Webman\Http\Response
     */
    public function downloadResourceByHash(Request $request)
    {
        try {
            $hash = $request->route->param('hash');
            $data = $this->service->get(['hash' => $hash]);
            if (empty($data)) {
                throw new AdminException('数据未找到', -1);
            }
            return response()->download($data->path, $data->filename);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * 上传图片
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function uploadImage(Request $request): \support\Response
    {
        try {
            $result = $this->service->upload();
            return Json::success('ok', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 上传文件
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function uploadFile(Request $request): \support\Response
    {
        try {
            $result = $this->service->upload('file');
            return Json::success('ok', $result);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
