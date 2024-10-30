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

trait DesignTrait
{


    /**
     * start
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function start(Request $request): \support\Response
    {
        return raw_view('common/design/panel/start');
    }

    /**
     * decision
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function decision(Request $request): \support\Response
    {
        return raw_view('common/design/panel/decision');
    }

    /**
     * task
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function task(Request $request): \support\Response
    {
        return raw_view('common/design/panel/task');
    }

    /**
     * custom
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function custom(Request $request): \support\Response
    {
        return raw_view('common/design/panel/custom');
    }

    /**
     * process
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function process(Request $request): \support\Response
    {
        return raw_view('common/design/panel/process');
    }

    /**
     * fork
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function fork(Request $request): \support\Response
    {
        return raw_view('common/design/panel/fork');
    }

    /**
     * join
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function join(Request $request): \support\Response
    {
        return raw_view('common/design/panel/join');
    }

    /**
     * subProcess
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function subProcess(Request $request): \support\Response
    {
        return raw_view('common/design/panel/subProcess');
    }

    /**
     * wfSubProcess
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function wfSubProcess(Request $request): \support\Response
    {
        return raw_view('common/design/panel/wfSubProcess');
    }

    /**
     * end
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function end(Request $request): \support\Response
    {
        return raw_view('common/design/panel/end');
    }

    /**
     * transition
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function transition(Request $request): \support\Response
    {
        return raw_view('common/design/panel/transition');
    }

    /**
     * detail
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function detail(Request $request): \support\Response
    {
        return raw_view('common/design/panel/detail');
    }

    /**
     * import
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     * @throws \Throwable
     */
    public function import(Request $request): \support\Response
    {
        return raw_view('common/design/panel/import');
    }
}
