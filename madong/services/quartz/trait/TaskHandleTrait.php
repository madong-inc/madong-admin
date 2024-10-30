<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */
namespace madong\services\quartz\trait;

use support\Request;

trait TaskHandleTrait
{

    /**
     * 审核处理页面
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function handle(Request $request): \support\Response
    {
        return raw_view('common/task/handle/index');
    }

    /**
     * 审核意见
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function handleApprove(Request $request): \support\Response
    {
        return raw_view('common/task/handle/handleApprove');
    }

    /**
     * 任务详情
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function detail(): \support\Response
    {
        return raw_view('common/task/detail/index');
    }

    /**
     * 任务详情-内置html表单
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function detail_idf(Request $request): \support\Response
    {
        $id          = $request->input('id');
        $operate     = $request->input('operate', 'add');
        $instanceUrl = $request->input('instance_url');
        $userInfo    = $this->adminInfo;
        return raw_view($instanceUrl, ['id' => $id, 'operate' => $operate, 'nickname' => $userInfo['nickname'] ?? '']);
    }

}
