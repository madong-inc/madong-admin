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

trait TrackTrait
{

    /**
     * 轨迹视图
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function track(Request $request): \support\Response
    {
        return raw_view('common/track/index');
    }

    /**
     * 流程图
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function trajectory(Request $request): \support\Response
    {
        $process_define_id   = $request->get('process_define_id');//流程定义ID
        $process_instance_id = $request->get('process_instance_id');//流程示例id
        $ingeniousEngines    = $this->services;
        $result              = $ingeniousEngines->processDefineService()->findById($process_define_id);
        $highLight           = $ingeniousEngines->processInstanceService()->highLight($process_instance_id);

        $data = [
            'viewer'        => true,
            'graphData'     => $result->getData('content') ?? [],
            'highLight'     => $highLight ?? [],
            'commitPath'    => '',
            'defaultConfig' => (object)['grid' => true],
        ];
        return raw_view('common/design/index', ['data' => json_encode($data)]);
    }

    /**
     * 时间线
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function timeline(Request $request): \support\Response
    {
        return raw_view('common/track/template/timeline');
    }

    /**
     * 时间表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function timetable(Request $request): \support\Response
    {
        return raw_view('common/track/template/timetable');
    }

}
